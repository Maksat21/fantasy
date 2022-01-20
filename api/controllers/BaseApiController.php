<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 19.02.18
 * Time: 12:01
 */

namespace api\controllers;


use api\models\helper\TokenHelper;
use api\models\helper\UserActivityHelper;
use common\models\AppVer;
use common\models\helpers\DeviceVersionHelper;
use common\models\mUser;
use common\models\Terminal;
use Yii;
use yii\rest\Controller;
use yii\web\ConflictHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class BaseApiController extends Controller {

    public $deviceType = '';
    public $userId = null;
    public $userIin = null;

    /**
     * @param $action
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action){
//        header('Access-Control-Allow-Origin: *');
//        $this->disableApi();
        $headers = \Yii::$app->request->getHeaders();
        if(\Yii::$app->controller->id != 'auth' && \Yii::$app->controller->id != 'front' && !isset($headers['authorization'])){
//            throw new NotFoundHttpException('N F');
        }

            if(!$this->checkAppVersion($headers) && \Yii::$app->controller->id != 'front'){
            throw new NotAcceptableHttpException(Yii::t('api', 'Update application'));
        }
//echo Yii::$app->controller->id;
        if(Yii::$app->controller->id != 'auth'){
            $token = TokenHelper::getToken($headers);
            $user = Terminal::findOne(['access_token' => $token]);
            if(!$user){
                throw new UnauthorizedHttpException('Под Вашим аккаунтом вошли с другого устройства');
            }else{
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
    public function getDecodedHeaderData(){
        $headerData = Yii::$app->request->getHeaders();
        $data = [];
        foreach ($headerData as $key => $header){
            $data[$key] = $this->decodeData($header[0]);
        }
        return $data;
    }

    /**
     * Получение декодированных данных тела
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getDecodedBodyData(){
        $bodyData = Yii::$app->request->getBodyParams();
        $data = [];
        foreach ($bodyData as $key => $header){
            $data[$key] = $this->decodeData($header);
        }
        return $data;
    }

    /**
     * Декодирование POST запроса
     * @param $postData
     * @return array
     */
    public function decodePostData($postData){
        $data = [];
        foreach ($postData as $k => $pD){
            $data[$k] = $this->decodeData($pD);
        }
        return $data;
    }

    /**
     * Декодирование данных
     * @param $data
     * @return bool|string
     */
    public function decodeData($data){
        return $data ? base64_decode($data) : false;
    }

    /**
     * Получение дополнительных данных
     * @return mixed
     */
    public function getSpecificData(){
        $request = Yii::$app->request;
        $data['ip'] = $request->userIP;
        $data['userData'] = $request->userAgent;
        $data['deviceType'] = ($this->deviceType) ? $this->deviceType : $this->getDeviceType();
        $data['appver']     = $request->headers['appver'];
        return $data;
    }

    /**
     * Проверка версии приложения
     * @param $header
     * @return bool
     */
    private function checkAppVersion($header){
        $userVersion = isset($header['appver']) ? $header['appver'] : false;
//        print_r($userVersion); die;
        if(!$userVersion){
            return false;
        }
        $appVer = Yii::$app->params['appVer'];
        if(!$appVer){
            return false;
        }
        $currentVersion = explode(',', $appVer);
        return (in_array($userVersion, $currentVersion)) ? true : false;
    }

    /**
     * Отключение API
     * @throws ConflictHttpException
     */
    private function disableApi(){
        throw new ConflictHttpException('Ведутся технические работы. Приносим извинения за доставленные неудобства.');
    }
}
