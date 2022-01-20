<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 01.04.2021
 * Time: 14:06
 */

namespace api\models\forms;


use common\models\User;
use yii\base\Model;

class TokenForm extends Model
{
    public $token;
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function rules()
    {
        return [
            [['token', 'userId'], 'required'],
            [['token'], 'string'],
            [['userId'], 'integer'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this;
        }
        $user = User::findOne(['id' => $this->userId]);
        if (!$user) {
            $this->addErrors(['userId' => 'Нет пользователя  таким id']);
            return false;
        }
        $user->f_token = $this->token;
        if ($user->save()) {
            return $user;
        } else {
            return false;
        }
    }
}