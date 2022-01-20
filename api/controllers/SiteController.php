<?php
namespace api\controllers;

use common\models\cashBack\Retail;
use common\models\Cities;
use common\models\delivery\DeliveryOrder;
use common\models\delivery\FoodMenu;
use common\models\delivery\OrderItem;
use common\models\Profile;
use common\models\Regions;
use common\models\User;
use telebot\controllers\DeliveryTelebotController;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return array
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => true];
    }

//    public function actionCheckOrders($orderId)
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        if ($orderId){
//            $order = DeliveryOrder::findOne($orderId);
//            $result = DeliveryOrder::createCourierXlsx($order);
//            if ($result){
//                $this->sendMailModerator($orderId);
//                $this->sendMailRetail($order);
//                return ['status' => true];
//            } else {
//                $this->sendMailModeratorError($orderId);
//                return ['status' => true];
//            }
//        }
//        return ['status' => false];
//    }

}
