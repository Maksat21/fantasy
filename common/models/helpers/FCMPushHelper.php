<?php

namespace common\models\helpers;


/**
 * Class FCMPushHelper
 * @package common\models\helpers
 */
class FCMPushHelper
{
    const ACTION_WAKEUP = 1;

    public static function baseNotification($title, $description, $token)
    {
        $key = \Yii::$app->params['fcm']['apiKey'];
        $api_url = \Yii::$app->params['fcm']['url'];
        $headers = [
            'Authorization:  key=' . $key,
            'Content-Type: application/json'
        ];

        $fields = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $description,
            ]
        ];
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close($ch);
        return json_decode($response, true);
    }
}