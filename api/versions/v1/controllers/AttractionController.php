<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 27.03.2018
 * Time: 13:07
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use api\models\forms\CheckCardForm;
use api\models\forms\TransactionCountForm;
use api\models\forms\TransactionListForm;
use common\models\Attraction;
use common\models\Card;
use common\models\helpers\ErrorMsgHelper;
use common\models\Slider;
use common\models\Terminal;
use common\models\Transaction;
use Yii;
use yii\web\ConflictHttpException;

//use common\models\SliderTracker;
//use common\models\UserActivity;

/**
 * Class FrontController
 * @package api\versions\v1\controllers
 */
class AttractionController extends BaseApiController
{

    // Вывод списка аттракционов
    public function actionTransactionCount()
    {
        $terminal = Terminal::findOne($this->userId);
        if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
            $attraction = Transaction::find()
                ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                ->where(["terminal.attraction_id" => $terminal->attraction_id])
                ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00")])
                ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59", strtotime('+1 day'))])
                ->count();
        } else {
            $attraction = Transaction::find()
                ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                ->where(["terminal.attraction_id" => $terminal->attraction_id])
                ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00", strtotime('-1 day'))])
                ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59")])
                ->count();
        }

        return ['status' => 200, 'count' => $attraction];
    }

    // Вывод списка аттракционов
    public function actionTransactionCountByAttraction()
    {
        $transactionCountForm = new TransactionCountForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if ($transactionCountForm->load($formData, '') && $transactionCountForm->validate()) {
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $attraction = Transaction::find()
                    ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                    ->where(["terminal.attraction_id" => $transactionCountForm->attraction])
                    ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00")])
                    ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59", strtotime('+1 day'))])
                    ->count();
            } else {
                $attraction = Transaction::find()
                    ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                    ->where(["terminal.attraction_id" => $transactionCountForm->attraction])
                    ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00", strtotime('-1 day'))])
                    ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59")])
                    ->count();
            }

            return ['status' => 200, 'count' => $attraction];
        }

    }

    /**
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCheckCard()
    {
        $checkCardForm = new CheckCardForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if ($checkCardForm->load($formData, '') && $checkCardForm->validate()) {
            $card = Card::findOne(['code' => $checkCardForm->card_code]);
            if ($card) {
                return ['status' => 200, 'id' => $card->id, 'balance' => $card->balance, 'is_active' => $card->status];
            } else {
                throw new ConflictHttpException(Yii::t('api', 'NOT_FOUND'));
            }
        }
    }

    // Вывод списка аттракционов
    public function actionList()
    {
        $list = [];
        $attraction = Attraction::find()
            ->select(['id', 'name'])
//            ->where(["status" => Attraction::STATUS_ACTIVE])
            ->asArray()
            ->all();

        foreach ($attraction AS $item) {
            $list[] = (object)[
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        }

        return ['attractions' => $list];
    }

    /**
     * Вывод списка транзакций
     * @return array|\yii\db\ActiveRecord[]
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionTransactionList()
    {
        $transactionListForm = new TransactionListForm();
        $formData = Yii::$app->request->getBodyParams();
        $requestData = $this->getSpecificData();
        $formData = array_merge($formData, $requestData);

        if ($transactionListForm->load($formData, '') && $transactionListForm->validate()) {
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $transaction = Transaction::find()
                    ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                    ->select(['transaction.id', 'ROUND(transaction.amount) as amount', 'transaction.created_at'])
                    ->where(["transaction.attraction_id" => $transactionListForm->attraction])
                    ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00")])
                    ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59", strtotime('+1 day'))])
                    ->orderBy(['transaction.id' => SORT_DESC])
                    ->asArray()
                    ->all();
            } else {
                $transaction = Transaction::find()
                    ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                    ->select(['transaction.id', 'ROUND(transaction.amount) as amount', 'transaction.created_at'])
                    ->where(["transaction.attraction_id" => $transactionListForm->attraction])
                    ->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00", strtotime('-1 day'))])
                    ->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59")])
                    ->orderBy(['transaction.id' => SORT_DESC])
                    ->asArray()
                    ->all();
            }


            return ['transactions' => $transaction];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($transactionListForm));
        }

    }

}