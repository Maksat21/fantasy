<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 4/29/19
 * Time: 3:01 PM
 */

namespace api\models\forms;


use common\models\helpers\ErrorMsgHelper;
use common\models\helpers\QrCodeHelper;
use common\models\MUser;
use yii\base\Model;

class PinCodeChangeForm extends Model
{
    public $pin1;
    public $pin2;
    public $userId;
    public $password;
    public function rules()
    {
        return [
            [['pin1', 'pin2'], 'required'],
            ['password', 'required', 'message' => 'Вы не ввели пароль!'],
            ['pin1', 'compare', 'compareAttribute' => 'pin2', 'message' => 'PIN не совпадают'],
        ];
    }

    public function setPin(){
        $status = false;
        $user = mUser::findOne($this->userId);
        if(!$user->validatePassword($this->password)){
            $this->addError('password', 'Вы ввели неверный пароль!');
            return $status;
        }
        if($user){
            $user->setPinCode($this->pin1);
            if($user->save()){
                $status = true;
            }else{
                $this->addError('pin1', ErrorMsgHelper::getErrorMsg($user));
            }
        }else{
            $this->addError('pin1', 'Пользователь не существует');
        }
        return $status;
    }
}