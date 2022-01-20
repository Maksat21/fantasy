<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 01.04.2021
 * Time: 14:18
 */

namespace api\models\forms;


use common\models\Message;
use common\models\User;
use yii\base\Model;

class MessageForm extends Model
{
    public $subject;
    public $action;
    public $description;
    public $userId;

    public function rules()
    {
        return [
            [['subject', 'action', 'description'], 'required'],
            [['subject', 'description'], 'string'],
            [['action', 'userId'], 'integer'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $users = [];
        if ($this->action == Message::ACTION_PERSONAL) {
            $users[] = $this->userId;
        } elseif ($this->action == Message::ACTION_ALL) {
            $us = User::find()->all();
            foreach ($us as $u) {
                $users[] = $u->id;
            }
        } else {
            $role = $this->action == Message::ACTION_CASHIER ? User::ROLE_CASHIER : $this->action == Message::ACTION_OPERATOR ? User::ROLE_ADMIN : User::ROLE_ACCOUNTANT;
            $us = User::findAll(['role' => $role]);
            foreach ($us as $u) {
                $users[] = $u->id;
            }
        }
        foreach ($users as $user) {
            $message = new Message();
            $message->subject = $this->subject;
            $message->description = $this->description;
            $message->user_id = $user;
            $message->status = Message::STATUS_NOT_SEND;
            $message->save();
        }
        return true;
    }
}