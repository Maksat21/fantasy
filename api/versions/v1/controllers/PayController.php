<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 18.05.2018
 * Time: 5:09
 */

namespace api\versions\v1\controllers;

use api\controllers\BaseApiController;
use api\models\forms\AddCardForm;
use api\models\forms\CardHistoryForm;
use api\models\forms\PaymentAdminForm;
use api\models\forms\PaymentForm;
use common\models\Attraction;
use common\models\Card;
use common\models\CloudPayments;
use common\models\helpers\ErrorMsgHelper;
use common\models\MUser;
use common\models\PayOut;
use common\models\PaySmsReport;
use common\models\Replenishment;
use common\models\Terminal;
use common\models\Transaction;
use common\models\UserCard;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ConflictHttpException;

class PayController extends BaseApiController
{

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPayment()
    {
        $paymentForm = new PaymentForm();
        $terminal = Terminal::findOne($this->userId);
        $attraction = Attraction::findOne($terminal->attraction_id);
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if($paymentForm->load($formData, '') && $paymentForm->validate()){
            $card = Card::findOne(['code' => $paymentForm->card]);
            if($card) {
                if($card->pay($attraction->price * $paymentForm->count)) {
//                    $sessionId = FrontHelper::getSession($this->userId);
                    $result = Transaction::createTransation($this->userId, $card->id, $attraction->price, $sessionId = null, $paymentForm->count, $attraction->id);
                    if ($result) {
                        if ($attraction->pay($paymentForm->count)) {
                            return ['status' => 200, 'is_pay' => true, 'balance' => $card->balance, 'payed' => $attraction->price * $paymentForm->count];
                        } else {
                            return ['status' => 200, 'is_pay' => false, 'message' => 'TRANSACTION_CREATE_ERROR', 'balance' => $card->balance];
                        }
                    } else {
                        return ['status' => 200, 'is_pay' => false, 'message' => 'ATTRACTION_PAY_ERROR', 'balance' => $card->balance];
                    }
                } else {
                    return ['status' => 200, 'is_pay' => false, 'message' => 'CARD_PAY_ERROR',  'balance' => $card->balance];
                }
            } else {
                return ['status' => 200, 'is_pay' => false, 'message' => "CARD_OR_ATTRACTION_NOT_FOUND", 'balance' => $card->balance];
            }
        }
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\ConflictHttpException
     */
    public function actionPaymentAdmin()
    {
        $paymentAdminForm = new PaymentAdminForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if ($paymentAdminForm->load($formData, '') && $paymentAdminForm->validate()) {
            $attraction = Attraction::findOne(['id' => $paymentAdminForm->attraction]);
            $card = Card::findOne(['code' => $paymentAdminForm->card]);
            if ($card && $attraction) {
                if ($card->pay($attraction->price * $paymentAdminForm->count)) {
//                    $sessionId = FrontHelper::getSession($this->userId);
                    if (Transaction::createTransation($this->userId, $card->id, $attraction->price, $sessionId = null, $paymentAdminForm->count, $attraction->id)) {
                        if ($attraction->pay($paymentAdminForm->count)) {
                            return ['status' => 200, 'is_pay' => true, 'balance' => $card->balance, 'payed' => $attraction->price * $paymentAdminForm->count];
                        } else {
                            return ['status' => 200, 'is_pay' => false, 'message' => 'TRANSACTION_CREATE_ERROR', 'balance' => $card->balance];
                        }
                    } else {
                        return ['status' => 200, 'is_pay' => false, 'message' => 'ATTRACTION_PAY_ERROR', 'balance' => $card->balance];
                    }
                } else {
                    return ['status' => 200, 'is_pay' => false, 'message' => 'CARD_PAY_ERROR', 'balance' => $card->balance];
                }
            } else {
                return ['status' => 200, 'is_pay' => false, 'message' => "CARD_OR_ATTRACTION_NOT_FOUND", 'balance' => $card->balance];
            }
        }
    }

    /**
     * Вывод истории карты
     * @return array
     * @throws InvalidConfigException
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCardHistory()
    {
        $form = new CardHistoryForm();
        if ($form->load(\Yii::$app->request->post(), '') && $form->validate()) {
            $card = Card::findOne(['code' => $form->code]);
            if (!$card) {
                throw new ConflictHttpException('Нет карточки');
            }
            $replenishments = Replenishment::find()
                ->leftJoin('user', 'user.id=replenishment.user_id')
                ->select(['replenishment.amount', 'replenishment.created_at', 'user.fullname'])
                ->where(['card_id' => $card->id])
                ->asArray()
                ->all();

            $list = [];

            foreach ($replenishments AS $replenishment) {
                $list[] = (object)[
                    'amount' => $replenishment['amount'],
                    'type' => 0,
                    'date' => $replenishment['created_at'],
                    'description' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $replenishment['fullname']),
                ];
            }

            $transactions = Transaction::find()
                ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                ->leftJoin('attraction', 'attraction.id=transaction.attraction_id')
                ->select(['attraction.name', 'amount', 'transaction.created_at'])
                ->where(['card_id' => $card->id])
                ->asArray()
                ->all();

            foreach ($transactions AS $transaction) {
                $list[] = (object)[
                    'amount' => $transaction['amount'],
                    'type' => 1,
                    'date' => $transaction['created_at'],
                    'description' => $transaction['name'],
                ];
            }

            array_multisort(array_column($list, 'date'), SORT_DESC, $list);

            // Не верное время что то с таимзоной
            foreach ($list as $l) {
                $l->date = \Yii::$app->formatter->asDatetime($l->date, 'php:H:i d.m.Y');
            }

            return [
                "status" => 200,
                "cardHistory" => $list
            ];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }
}