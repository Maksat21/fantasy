<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 19.02.18
 * Time: 16:49
 */

namespace api\versions\v1\controllers;


use api\controllers\FrontApiController;
use api\models\forms\ForgotPassForm;
use api\models\forms\FrontLoginForm;
use api\models\helper\TokenHelper;
use common\models\helpers\ErrorMsgHelper;
use common\models\Replenishment;
use common\models\Session;
use Yii;
use yii\web\ConflictHttpException;

class FrontAuthController extends FrontApiController
{

    public function beforeAction($action)
    {
        if (!\Yii::$app->request->post()) {
            //           throw new NotFoundHttpException();
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
        $loginForm = new FrontLoginForm();

        if ($loginForm->load($data, '') && $loginForm->validate()) {
            $user = $loginForm->_user;
            $token = TokenHelper::createToken($user->id, $request->userAgent, $this->deviceType);
            $user->access_token = $token['access_token'];

            $incompleteSessions = Session::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['session_end' => null])
                ->all();

            if($incompleteSessions) {
                unset($incompleteSessions[count($incompleteSessions) - 1]);

                foreach ($incompleteSessions as $item) {
                    $transactions = Replenishment::find()
                        ->select(['sum(amount) as sum', 'count(amount) as count'])
                        ->where(['session_id' => $item->id])
                        ->asArray()
                        ->one();

                    $item->transaction_count = $transactions['count'] ? $transactions['count'] : 0;
                    $item->transaction_amount = $transactions['sum'] ? $transactions['sum'] : 0;
                    $item->session_end = date('Y-m-d H:i:s');
                    $item->save();
                }
                if ($user->save()) {
                    return ['status' => 200, 'user' => $user->getData()];
                } else {
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
                }
            } else {
                $session = new Session();
                $session->user_id = $user->id;
                $session->transaction_count = 0;
                $session->transaction_amount = 0;

                if ($user->save()
                    && $session->save()
                ) {
                    return ['status' => 200, 'user' => $user->getData()];
                } else {
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
                }
            }




        } else {
//                die;
//                return ['status' => 400, 'message' => ErrorMsgHelper::getErrorMsg($loginForm)];
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
//                    return ['status' => 400, 'message' => ErrorMsgHelper::getErrorMsg($forgotPassForm->_user)];
                    throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($forgotPassForm->_user));
                }
            } else {
                throw new ConflictHttpException(Yii::t('api', 'User not found'));
            }
        } else {
//            return ['status' => 400, 'message' => ErrorMsgHelper::getErrorMsg($forgotPassForm)];
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