<?php

namespace common\components;

use Yii;
use common\models\Callback;
use yii\base\Component;
use yii\httpclient\Client;

/**
 * Class CallbackNotify
 * @package common\components
 */
class CallbackNotify extends Component
{
    private $model;

    /**
     * CallbackNotify constructor.
     * @param Callback $model
     * @param array $config
     */
    public function __construct(Callback $model, $config = [])
    {
        $this->model = $model;

        parent::__construct($config);
    }

    public function sendEmail()
    {
        return Yii::$app->mailer->compose(['html' => 'newCallback-html'], ['model' => $this->model])
            ->setFrom(Yii::$app->params['robotEmail'])
            ->setTo(Yii::$app->params['supportEmail'])
            ->setSubject(Yii::t('callback', 'New callback order from {phone}', ['phone' => $this->model->phone]))
            ->send();
    }

    public function sendSms()
    {
        $client = new Client();
        $client->createRequest()
            ->setMethod('get')
            ->setUrl('http://sms.ru/sms/send')
            ->setData([
                    'api_id' => Yii::$app->params['smsApiKey'],
                    'to' => Yii::$app->params['smsPhone'],
                    'text' => Yii::t('callback', 'New callback order from {phone}', ['phone' => $this->model->phone])
                ]
            )
            ->send();
    }
}