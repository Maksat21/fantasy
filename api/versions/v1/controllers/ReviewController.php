<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 27.03.2018
 * Time: 13:07
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use api\models\helper\UserActivityHelper;
use common\models\helpers\ErrorMsgHelper;
use common\models\Review;
use common\models\Slider;
//use common\models\SliderTracker;
//use common\models\UserActivity;
use yii\web\ConflictHttpException;

class ReviewController extends BaseApiController
{
    // Вывод списка отзывов
    public function actionList(){
        $reviews = Review::find()->where(["status" => Review::STATUS_ACTIVE])->all();
        return ['status' => 200, 'reviews' => $reviews];
    }
}