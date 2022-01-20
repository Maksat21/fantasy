<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 19.02.18
 * Time: 16:49
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use api\models\forms\ForgotPassForm;
use api\models\forms\LoginForm;
use api\models\helper\TokenHelper;
use common\models\Attraction;
use common\models\CertDatabase;
use common\models\CloudPayments;
use common\models\helpers\ErrorMsgHelper;
use common\models\mUser;
use common\models\SmsReport;
use common\models\Terminal;
use Yii;
use yii\web\ConflictHttpException;
use yii\web\NotFoundHttpException;

class AuthController extends BaseApiController
{

    public function beforeAction($action)
    {
        if (!\Yii::$app->request->post()) {
            throw new NotFoundHttpException();
        }
        return parent::beforeAction($action);
    }

    /**
     * Авторизация
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSignIn()
    {
        $request = Yii::$app->request;
        $data = $this->getDecodedBodyData();
        $loginForm = new LoginForm();
        $attractionName = null;

        if ($loginForm->load($data, '') && $loginForm->validate()) {
            $user = $loginForm->_user;
            if (!$this->deviceType) $this->deviceType = $this->getDeviceType();
            $token = TokenHelper::createToken($user->id, $request->userAgent, $this->deviceType);
            $user->access_token = $token['access_token'];
            if ($loginForm->ftoken) {
                $user->f_token = $loginForm->ftoken;
            }
            $osType = $this->deviceType == 'mobileIOS' ? Terminal::OS_TYPE_IOS : Terminal::OS_TYPE_ANDROID;
            $user->os_type = $osType;
            $header = Yii::$app->request->getHeaders();
            $attraction = Attraction::findOne($user->attraction_id);
            if ($attraction) {
                $attractionName = $attraction->name;
            }
            $user->app_ver = $header['appVer'];

            if ($user->save()) {
                return ['status' => 200, 'parkName' => Yii::$app->params['parkName'], 'attractionName' => $attractionName, 'user' => $user->getData()];
            } else {
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
            }
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($loginForm));
        }
    }

    /**
     * Send sms to user for reset password request
     * @return array
     * @throws ConflictHttpException
     */
//    public function actionResetPasswordRequest(){
//        $resetPasswordForm = new ResetPasswordForm();
//        $data  = $this->getDecodedBodyData();
//        $data = mUser::checkUser1C($checkUserForm->iin);
//        if($data) {
//            return ['status' => 200, 'data' => $data];
//        } else {
//            throw new ConflictHttpException(\Yii::t('api', 'User Not Found'));
//        }
//        if($resetPasswordForm->load($data, '') && $resetPasswordForm->validate()) {
//
//        }
//        $smsForm = new SmsForm();
//        if($smsForm->load($data, '') && $smsForm->validate()){
//            $smsReport = new SmsReport();
//            $smsReport->username = $smsForm->phone;
//            $smsReport->code = password_hash($smsForm->code, PASSWORD_BCRYPT);
//            $smsReport->created  = (string)time();
//            if($smsReport->save()){
//                $msg = 'Ваш код для системы «Pillikan»:'.$smsForm->code;
//                SmsHelper::send($smsReport->phone, $msg);
//                return [
//                    'status' => 200
//                ];
//            } else {
//                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($smsReport));
//            }
//        }else{
//            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($smsForm));
//        }
//    }

    /**
     * Востановление пароля
     * @return array
     * @throws ConflictHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionReset()
    {
        $forgotPassForm = new ForgotPassForm();
        $request = Yii::$app->request;
        $data = $this->getDecodedBodyData();
        if ($forgotPassForm->load($data, '') && $forgotPassForm->validate()) {
            $forgotPassForm->_user = mUser::findOne(['phone' => $forgotPassForm->phone, 'iin' => $forgotPassForm->iin, 'status' => mUser::STATUS_ACTIVE]);
            if ($forgotPassForm->_user) {
                $forgotPassForm->_user->setPassword($forgotPassForm->password);
                if (!$this->deviceType) $this->deviceType = $this->getDeviceType();
                if ($forgotPassForm->_user->save()) {
                    return ['status' => 200];
                } else {
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($forgotPassForm->_user));
                }
            } else {
                throw new ConflictHttpException(Yii::t('api', 'User not found'));
            }
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($forgotPassForm));
        }
    }


    /**
     *
     */
    public function actionTest()
    {

    }
}