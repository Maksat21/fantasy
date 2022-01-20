<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 18.05.2018
 * Time: 5:10
 */

namespace api\models\forms;


use common\models\helpers\ErrorMsgHelper;
use common\models\MUser;
use common\models\PayOut;
use common\models\PaySmsReport;
use Yii;
use yii\base\Model;
use yii\web\ConflictHttpException;

class PayOutForm extends Model
{
    public $operator;
    public $sum;
    public $ball;
    public $userId;
    public $_user;
    public $payOutId;
    public $code;
    public $number;
    public function rules()
    {
        return [
            [['operator', 'sum', 'userId', 'code', 'number', 'ball'], 'required'],
            ['operator', 'integer'],
            ['sum', 'number'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if(!parent::validate($attributeNames, $clearErrors)){
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($this));
        }
        $this->_user = MUser::find()->where(['id' => $this->userId, 'status' => MUser::STATUS_ACTIVE])->one();
        if(!$this->_user){
            throw new ConflictHttpException(\Yii::t('api', 'User not found'));
        }
        $smsRequest = PaySmsReport::find()->where(['user_id'=>$this->_user])
            ->orderBy(['created_at' => SORT_DESC])->asArray()->one();
        if(!$smsRequest){
            throw new ConflictHttpException(Yii::t('api', 'There is no request for pay out'));
        }
        $min15ago = strtotime('16 min ago', time());
        if($smsRequest['created_at'] < $min15ago){
            throw new ConflictHttpException(Yii::t('api', 'Request is too old. Please, try again'));
        }
        if(!password_verify($this->code, $smsRequest['code'])){
            throw new ConflictHttpException(Yii::t('api', 'Wrong code'));
        }
        $payOutMinSum = Yii::$app->params['payOutMinSum'];
        if($this->_user->balance < $payOutMinSum){
            throw new ConflictHttpException(Yii::t('api', 'For pay out need balance: '.$payOutMinSum));
        }
        if($this->sum < 100){
            throw new ConflictHttpException('Минимальная суммма выплаты 100 тг.');
        }
        $limitCheck = $this->isLimitByAmount($this->sum);
        if($limitCheck['isLimited']){
            throw new ConflictHttpException('Лимит на вывод в месяц - 3000 тг. Доступная сумма на вывод - '
                . $limitCheck['allowedAmount'] . 'тг.');
        }

        if($this->_user->balance < $this->ball){
            throw new ConflictHttpException(\Yii::t('api', 'Not enough balance'));
        }

        return true;
    }

    private function isLimitByAmount($payOutSum){
        $firstDate = strtotime(date('Y-m-01 00:00:01'));
        $lastDate  = strtotime(date('Y-m-t 23:59:59'));
        $totalSum = PayOut::find()->where(['m_user' => $this->userId])
            ->andWhere('status <> '.PayOut::PAY_OUT_STATUS_CANCELED)
            ->andWhere('created_at >= '. $firstDate . ' AND created_at <= '. $lastDate)
            ->sum('p_sum');
        $allowedAmount = 3000;

        $isLimit = $payOutSum + $totalSum > $allowedAmount;
        if($isLimit){
            $allowedAmount = $allowedAmount - ($payOutSum + $totalSum);
        }
        $result['isLimited'] = $isLimit;
        $result['allowedAmount'] = $allowedAmount;
        return $result;
    }

    public function save(){
        $payOut = new PayOut();
        $time = time();
        $payOut->created_at = $time;
        $payOut->m_user = $this->userId;
        $payOut->p_sum = $this->sum;
        $payOut->updated_at = $time;
        $payOut->operator_id = $this->operator;
        $payOut->status = PayOut::PAY_OUT_STATUS_WAITING;
        $payOut->number = $this->_user->username;
        $payOut->in_ball = $this->ball;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if($payOut->save()){
                $this->_user->balance = $this->_user->balance - $this->ball;
                if($this->_user->save()){
                    $transaction->commit();
                    return true;
                }else{
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($this->_user));
                }
            }else{
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($payOut));
            }
        }catch (\Exception $e){
            $transaction->rollBack();
            throw new ConflictHttpException($e->getMessage());
        }catch (\Throwable $t){
            $transaction->rollBack();
            throw new ConflictHttpException($t->getMessage());
        }
    }
}