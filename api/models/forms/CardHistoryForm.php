<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 07.05.2021
 * Time: 10:01
 */

namespace api\models\forms;


use yii\base\Model;

class CardHistoryForm extends Model
{
    public $code;

    public function rules()
    {
        return [
            [['code'], 'integer'],
            [['code'], 'required'],
        ];
    }
}