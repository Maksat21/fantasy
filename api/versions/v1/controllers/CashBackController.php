<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 5/13/19
 * Time: 1:04 PM
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiRetailController;
use common\models\cashBack\Payment;
use common\models\cashBack\Purchases;
use Yii;

class CashBackController extends BaseApiRetailController
{
    public function actionHistoryIn(){
        $userId = $this->userId;
        $dateFrom = Yii::$app->request->post('from');
        $dateTo   = Yii::$app->request->post('to');
        $lastId   = Yii::$app->request->post('last_id');
        if(!$dateFrom){
            $dateFrom = date("Y-m-d H:i:s", strtotime('first day of this month'));
        }else{
            $dateFrom = date("Y-m-d H:i:s", strtotime($dateFrom . " 00:00:00"));
        }
        if(!$dateTo){
            $dateTo = date("Y-m-d H:i:s", time());
        }else{
            $dateTo = date("Y-m-d H:i:s", strtotime($dateTo . " 23:59:59"));
        }
        $query = Purchases::find()->select('p.id, r.l_name, r.f_name, p.cash_back, p.created_at, p.cb_amount as amount')
            ->from(Purchases::tableName() . ' p')
            ->leftJoin('m_profile r', 'r.user_id = p.user_id')
            ->where("p.retail_id = ".$userId. " AND p.status = 2 AND p.created_at >= '".$dateFrom."' AND p.created_at <= '".$dateTo. "'");

        if($lastId){
            $query->andWhere("p.id < ".$lastId);
        }
        $query->limit(10)->orderBy(['created_at' => SORT_DESC]);
        $sqlTotal = "SELECT COUNT(*) FROM cb_purchases p ".
            "WHERE p.retail_id = $userId AND p.status = 2 AND p.created_at >= '".$dateFrom. "' AND p.created_at <= '".$dateTo."'";
        $history = $query->asArray()->all();
        $totalElements = Yii::$app->db->createCommand($sqlTotal)->queryScalar();
        return ['list' => $history, 'totalElements' => $totalElements];
    }

    public function actionHistoryOut(){
        $userId = $this->userId;
        $dateFrom = Yii::$app->request->post('from');
        $dateTo   = Yii::$app->request->post('to');
        $lastId   = Yii::$app->request->post('last_id');
        if(!$dateFrom){
            $dateFrom = date("Y-m-d H:i:s", strtotime('first day of this month'));
        }else{
            $dateFrom = date("Y-m-d H:i:s", strtotime($dateFrom . " 00:00:00"));
        }
        if(!$dateTo){
            $dateTo = date("Y-m-d H:i:s", time());
        }else{
            $dateTo = date("Y-m-d H:i:s", strtotime($dateTo . " 23:59:59"));
        }
        $query = Payment::find()->select("p.id, r.l_name, r.f_name, p.created_at, p.real_amount as amount")
            ->from(Payment::tableName() . ' p')
            ->leftJoin("m_profile r", "r.user_id = p.user_id")
            ->where("p.retail_id = $userId AND p.status = 2 AND p.created_at >= '".$dateFrom. "' AND p.created_at <= '".$dateTo."'");
        if($lastId){
           $query->andWhere("p.id < ".$lastId);
        }
        $query->limit(10)->orderBy(['created_at' => SORT_DESC]);
        $sqlTotal = "SELECT COUNT(*) FROM cb_payment p ".
            "WHERE p.retail_id = $userId AND p.status = 2 AND p.created_at >= '".$dateFrom. "' AND p.created_at <= '".$dateTo."'";
        $history = $query->asArray()->all();
        $totalCount = Yii::$app->db->createCommand($sqlTotal)->queryScalar();
        return ['list' => $history, 'totalElements' => $totalCount];
    }
}