<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 16:55
 */

namespace api\models\forms;


use common\models\User;
use yii\base\Model;

class UpdateUserForm extends Model
{
    public $id;
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
            [['id', 'username', 'fullname', 'role'], 'required'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = User::findOne(['id' => $this->id]);
        if (!$user) {
            return false;
        }
        $user->username = $this->username;
        $user->fullname = $this->fullname;
        $user->status = $this->status;
        if ($this->password) {
            $user->setPassword($this->password);
        }
        $user->role = $this->role;
        if ($this->status == 0) {
            $user->status = User::STATUS_NOT_ACTIVE;
        } else {
            $user->status = User::STATUS_ACTIVE;
        }
        if ($user->save()) {
            return $user;
        } else {
            return false;
        }
    }
}