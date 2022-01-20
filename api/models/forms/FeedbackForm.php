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

class FeedbackForm extends Model
{
    public $name;
    public $phone;
    public $description;

    public function rules()
    {
        return [
          [['name', 'phone', "description"], 'string'],
          [['phone', 'phone', 'description'], 'required'],
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