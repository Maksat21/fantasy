<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.02.18
 * Time: 17:01
 */

namespace api\models\forms;


use yii\base\Model;

class SetPasswordForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['iin', 'password1', 'password2'], 'string'],
            [['iin', 'password1', 'password2'], 'required'],
        ];
    }
}