<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 27.03.2018
 * Time: 13:11
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use api\models\forms\FeedbackForm;
use api\models\helper\TokenHelper;
use common\models\Feedback;
use common\models\helpers\ErrorMsgHelper;
use common\models\MSupport;
use yii\web\ConflictHttpException;

class FeedbackController extends BaseApiController
{
    public function actionSend() {
        $feedbackForm  = new FeedbackForm();
        $formData    = $this->getDecodedBodyData();
        $requestData = $this->getSpecificData();
        $formData    = array_merge($formData, $requestData);
//print_r($formData); die;
        if($feedbackForm->load($formData, '') && $feedbackForm->validate()){
            $feedback = new Feedback();
            if($feedback->load($formData, '') && $feedback->validate()) {
                if($feedback->save()) {
                    return ['status' => 200];
                } else {
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($feedback));
                }
            }
        }

    }

}