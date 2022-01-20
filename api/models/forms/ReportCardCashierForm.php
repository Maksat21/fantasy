<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.03.2018
 * Time: 14:14
 */

namespace api\models\forms;


use common\models\helpers\ErrorMsgHelper;
//use common\models\MProfile;
use common\models\mUser;
use yii\base\Model;
use yii\web\ConflictHttpException;

class ReportCardCashierForm extends Model
{
    public $card_code;

    public function rules()
    {
        return [
          [['card_code'], 'string'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {

        if(!parent::validate($attributeNames, $clearErrors)){
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($this));
        }

        return true;
    }
}