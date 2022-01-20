<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 01.04.2021
 * Time: 14:38
 */

namespace backend\modules\cron\controllers;


use common\models\helpers\FCMPushHelper;
use common\models\Message;
use common\models\User;
use yii\web\Controller;

class MessageController extends Controller
{
    public function actionSend()
    {
        $messages = Message::findAll(['status' => Message::STATUS_NOT_SEND]);
        $results = [];
        foreach ($messages as $message) {
            $user = User::findOne(['id' => $message->user_id]);
            if ($user && $user->f_token) {
                try {
                    $status = FCMPushHelper::baseNotification($message->subject, $message->description, $user->f_token)['success'];
                    if ($status == true) {
                        $message->status = Message::STATUS_SEND;
                        $message->save();
                    } else {
                        $results[] = 'Безуспешная отправка сообщения пользователю с id ' . $user->id;
                    }
                } catch (\Exception $e) {
                    $results[] = "Ошибка при отправке сообщения пользователю с id " . $user->id;
                }
            } else {
                $results[] = 'Нет пользователя c id ' . $message->user_id . ' или у пользователя не установлен токен';
            }
        }
//        return $results;
    }
}