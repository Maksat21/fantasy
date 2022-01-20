<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 11/16/18
 * Time: 2:29 PM
 */

namespace api\models\forms;


use common\models\MUser;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $password;
    public $password2;
    public $userId;

    public function rules()
    {
        return [
            [['password', 'password2'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'password2', 'message' => 'Пароли не совпадают'],
        ];
    }

    public function change($userId){
        $user = mUser::findOne($userId);
//        print_r($this->userId); die;
        if(!$user){
            $this->addError('userId','Пользователь не найден');
            return false;
        }

        $user->setPassword($this->password);
        return $user->save();
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль 1',
            'password2' => 'Пароль 2'
        ];
    }
}