<?php
namespace common\components;

use Yii;
use common\models\Feedback;
use yii\base\Component;
use yii\httpclient\Client;

/**
 * Class FeedbackNotify
 * @package common\components
 */
class FeedbackNotify extends Component
{
    private $model;

    /**
     * FeedbackNotify constructor.
     * @param Feedback $model
     * @param $config
     */
    public function __construct(Feedback $model, $config =  [])
    {
        $this->model = $model;

        parent::__construct($config);
    }

    public function sendEmail()
    {
        return Yii::$app->mailer->compose(['html' => 'newFeedback-html'], ['model' => $this->model])
            ->setFrom(Yii::$app->params['robotEmail'])
            ->setTo(Yii::$app->params['supportEmail'])
            ->setSubject(Yii::t('feedback', 'New feedback from {name}', ['name' => $this->model->name]))
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
                    'text' => Yii::t('feedback', 'New feedback from {name}', ['name' => $this->model->name])
                ]
            )
            ->send();
    }
}