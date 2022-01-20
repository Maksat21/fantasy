<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 4/29/19
 * Time: 4:16 PM
 */

namespace api\models\forms;


use common\models\MUser;
use yii\base\Model;

class PinValidateForm extends Model
{
    public $pin;
    public $userId;

    public function rules()
    {
        return [
            ['pin', 'required', 'message' => 'Необходимо ввести PIN!'],
            ['userId', 'required']
        ];
    }

    public function validatePin(){
        $user = mUser::findOne($this->userId);
        if($user && $user->validatePinCode($this->pin)){
            return true;
        }else{
            $this->addError('pinCode', 'Неверный PIN код!');
            return false;
        }
    }
}