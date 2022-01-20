<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.02.18
 * Time: 17:01
 */

namespace api\models\forms;


use common\models\mUser;
use yii\base\Model;
use yii\web\ConflictHttpException;

class CheckPasswordForm extends Model
{
    public $_user;

    public $password;
    public $password2;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['password'], 'string'],
            [['password'], 'required'],
        ];
    }

    /**
     * @param null $attributeNames
     * @param null $clearErrors
     * @return bool
     * @throws ConflictHttpException
     */
    public function validate($attributeNames = null, $clearErrors = null)
    {
        if(!parent::validate($attributeNames, $clearErrors)){
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'iin' => 'ИИН',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'password2' => 'Пароль 2'
        ];
    }
}