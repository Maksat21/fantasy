<?php

namespace common\models\helpers;

use yii\base\Model;

class ErrorMsgHelper
{
    public static function getErrorMsg(Model $model){
        $errors  = $model->getFirstErrors();
        if($errors){
            $firstKey = array_keys($errors)[0];
            return $errors[$firstKey];
        }
        return '';
    }
}