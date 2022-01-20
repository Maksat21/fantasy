<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 14:25
 */

namespace api\models\forms;


use common\models\User;
use yii\base\Model;

class UserForm extends Model
{
    public $username;
    public $fullname;
    public $password;
    public $role;
    public $status;

    public function rules()
    {
        return [
            [['username', 'fullname', 'password'], 'string', 'max' => 255],
            [['role', 'status'], 'integer'],
            [['username', 'fullname', 'password', 'role', 'status'], 'required'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = new User();
        $user->username = $this->username;
        $user->fullname = $this->fullname;
        $user->setPassword($this->password);
        $user->role = $this->role;
        if($this->status == 0) {
            $user->status = User::STATUS_NOT_ACTIVE;
        } else {
            $user->status = User::STATUS_ACTIVE;
        }
        $user->generateAuthKey();
        if ($user->save()) {
            return $user;
        } else {
            return false;
        }
    }
}