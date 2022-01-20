<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 5/15/19
 * Time: 3:01 PM
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiRetailController;
use api\models\forms\RetailPinValidateForm;
use common\models\cashBack\RNotification;
use common\models\helpers\ErrorMsgHelper;
use Yii;
use yii\web\ConflictHttpException;

class RetailController extends BaseApiRetailController
{
    public function actionPin(){
        $model = new RetailPinValidateForm();
        $model->retailId = $this->userId;
        if($model->load(Yii::$app->request->post(), '') && $model->validate()){
            if($model->validatePin()){
                return ['status' => 200];
            }else{
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
            }
        }else{
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
        }
    }

    public function actionNotification(){
        $lastId = \Yii::$app->request->post('last_id');
        $query = RNotification::find()
            ->where(['status' => RNotification::STATUS_PUBLISHED])
            ->andWhere('retail_id is null or retail_id = '.$this->userId);

        if($lastId){
            $query->andWhere(['<','id', $lastId]);
        }
        $totalElements = RNotification::find()
            ->where(['status' => RNotification::STATUS_PUBLISHED])
            ->andWhere('retail_id is null or retail_id = '.$this->userId)->count();

        $list =  $query->limit(10)->orderBy(['id'=>SORT_DESC])->all();
        return ['status' => 200, 'list' => $list, 'totalElements' => $totalElements];
    }

    public function actionToken(){
        $fToken = Yii::$app->request->post('f_token');
        if(!$fToken){
            throw new ConflictHttpException('Пустой токен для firebase push');
        }
        $result = \Yii::$app->db->createCommand()->update('cb_retail',
            ['f_token' => $fToken],
            ['id' => $this->userId])
            ->execute();
        if($result){
            return ['status' => 200];
        }else{
            throw new ConflictHttpException('Ошибка при обновлении firebase токена');
        }
    }
}