<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 14.03.2018
 * Time: 19:54
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use common\models\Badge;
use common\models\News;
use yii\web\ConflictHttpException;

class NewsController extends BaseApiController
{
    public function actionList(){
        $badge = Badge::findOne(['user_id' => $this->userId]);
        if($badge){
            $badge->news = 0;
            $badge->save();
        }
        $lastDecodedId  = \Yii::$app->request->post('lastId');
        $lastId       = $this->decodeData($lastDecodedId);
        $query = News::find()->where(['status' => News::STATUS_PUBLISHED]);

        if($lastId){
            $query->andWhere(['<','id', $lastId]);
        }
        $list =  $query->limit(10)->orderBy(['created_at'=>SORT_DESC])->all();
        $totalElements = News::find()->where(['status' => News::STATUS_PUBLISHED])->count();
        return ['status' => 200, 'list' => $list, 'totalElements' => $totalElements];
    }

    public function actionView(){
        $decodeId = \Yii::$app->request->post('id');
        if(!$decodeId){
            throw new ConflictHttpException(\Yii::t('api', 'News not found'));
        }
        $id = $this->decodeData($decodeId);
        if(!$id){
            throw new ConflictHttpException(\Yii::t('api', 'News not found'));
        }
        $news = News::find()->where(['id' => $id, 'status' => News::STATUS_PUBLISHED])->one();
        return ['status' => 200, 'news' => $news];
    }
}
