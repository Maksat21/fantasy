<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.03.2018
 * Time: 14:14
 */

namespace api\models\forms;


use common\models\helpers\ErrorMsgHelper;
use yii\base\Model;
use yii\web\ConflictHttpException;

class PaymentForm extends Model
{
    public $card;
    public $count;

    public function rules()
    {
        return [
            [['card'], 'string'],
            [['count'], 'integer'],
            [['card', 'count'], 'required'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {

        if (!parent::validate($attributeNames, $clearErrors)) {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($this));
        }

        return true;
    }
}