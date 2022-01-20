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

class PaymentAdminForm extends Model
{
    public $card;
    public $attraction;
    public $count;

    public function rules()
    {
        return [
          [['card', 'attraction'], 'string'],
          [['count'], 'integer'],
          [['card', 'attraction'], 'required'],
        ];
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     * @throws ConflictHttpException
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {

        if(!parent::validate($attributeNames, $clearErrors)){
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($this));
        }

        return true;
    }
}