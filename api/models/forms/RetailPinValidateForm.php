<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 4/29/19
 * Time: 4:16 PM
 */

namespace api\models\forms;


use common\models\cashBack\Retail;
use yii\base\Model;

class RetailPinValidateForm extends Model
{
    public $pin;
    public $retailId;

    public function rules()
    {
        return [
            [['pin', 'retailId'], 'required']
        ];
    }

    public function validatePin(){
        $retail = Retail::findOne($this->retailId);
        if($retail && $retail->validatePinCode($this->pin)){
            return true;
        }else{
            $this->addError('pinCode', 'Неверный PIN код');
            return false;
        }
    }
}