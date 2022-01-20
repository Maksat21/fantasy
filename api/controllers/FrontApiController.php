<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 19.02.18
 * Time: 12:01
 */

namespace api\controllers;


use api\models\helper\TokenHelper;
use common\models\AppVer;
use common\models\helpers\DeviceVersionHelper;
use common\models\mUser;
use common\models\User;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\ConflictHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class FrontApiController extends Controller
{

    public $deviceType = '';
    public $userId = null;
    public $userIin = null;

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'cors' => Cors::className(),
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'authMethods' => [
//                        HttpBearerAuth::className(),
                    ],
                ]
            ]
        );
    }

    /**
     * @param $action
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {

//        header('Access-Control-Allow-Origin: *');
//        $this->disableApi();
        $headers = \Yii::$app->request->getHeaders();
        if (Yii::$app->controller->id != 'front-auth' && !isset($headers['authorization'])) {
            die;
//            return ['status' => 400, 'message' => 'Токен где БЛЕАТЬ?'];
//            throw new NotFoundHttpException('Токен где БЛЕАТЬ?');
//            throw new BadRequestHttpException('Токен где БЛЕАТЬ?');
//            throw new \Exception('Токен где БЛЕАТЬ?');
//            throw new UnauthorizedHttpException('TOKEN_NOT_FOUND');
        }

        if (Yii::$app->controller->id != 'front-auth') {

            $token = TokenHelper::getToken($headers);

            $user = User::findOne(['access_token' => $token]);
//            print_r($token); die;
            if (!$user) {
//                die;
//                return ['status' => 400, 'message' => 'Под Вашим аккаунтом вошли с другого устройства'];
                throw new UnauthorizedHttpException('Под Вашим аккаунтом вошли с другого устройства');
            } else {
//                $userId = TokenHelper::getUserIdByToken($token);
//                if($userId != $user->id){
//                    throw new UnauthorizedHttpException('Под Вашим аккаунтом вошли с другого устройства');
//                }
                $this->userId = $user->id;
//                print_r($user);
//                UserActivityHelper::save($this->userId);
            }
        }

        $this->deviceType = $this->getDeviceType();
        return parent::beforeAction($action);
    }

    /**
     * Получение типа устройства
     * @return mixed
     */
    public function getDeviceType()
    {
        return DeviceVersionHelper::getDeviceTypeByAppRequest(Yii::$app->request->userAgent);
    }

    /**
     * Получение декодированных данных заголовка
     * @return array
     */
    public function getDecodedHeaderData()
    {
        $headerData = Yii::$app->request->getHeaders();
        $data = [];
        foreach ($headerData as $key => $header) {
            $data[$key] = $this->decodeData($header[0]);
        }
        return $data;
    }

    /**
     * Декодирование данных
     * @param $data
     * @return bool|string
     */
    public function decodeData($data)
    {
        return $data ? base64_decode($data) : false;
    }

    /**
     * Получение декодированных данных тела
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getDecodedBodyData()
    {
        $bodyData = Yii::$app->request->getBodyParams();
        $data = [];
        foreach ($bodyData as $key => $header) {
            $data[$key] = $this->decodeData($header);
        }
        return $data;
    }

    /**
     * Декодирование POST запроса
     * @param $postData
     * @return array
     */
    public function decodePostData($postData)
    {
        $data = [];
        foreach ($postData as $k => $pD) {
            $data[$k] = $this->decodeData($pD);
        }
        return $data;
    }

    /**
     * Получение дополнительных данных
     * @return mixed
     */
    public function getSpecificData()
    {
        $request = Yii::$app->request;
        $data['ip'] = $request->userIP;
        $data['userData'] = $request->userAgent;
        $data['deviceType'] = ($this->deviceType) ? $this->deviceType : $this->getDeviceType();
        $data['appver'] = $request->headers['appver'];
        return $data;
    }

    /**
     * Проверка версии приложения
     * @param $header
     * @return bool
     */
    private function checkAppVersion($header)
    {
        $userVersion = isset($header['appver']) ? $header['appver'] : false;
//        print_r($userVersion); die;
        if (!$userVersion) {
            return false;
        }
        $appVer = Yii::$app->params['appVer'];
        if (!$appVer) {
            return false;
        }
        $currentVersion = explode(',', $appVer);
        return (in_array($userVersion, $currentVersion)) ? true : false;
    }

    /**
     * Отключение API
     * @throws ConflictHttpException
     */
    private function disableApi()
    {
        throw new ConflictHttpException('Ведутся технические работы. Приносим извинения за доставленные неудобства.');
    }
}
