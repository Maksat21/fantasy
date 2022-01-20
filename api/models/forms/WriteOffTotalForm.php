<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 15:24
 */

namespace api\models\forms;


use common\models\Transaction;
use yii\base\Model;

class WriteOffTotalForm extends Model
{

    public $dateFrom;
    public $dateTo;


    /**
     * @param $models
     * @return array
     * @throws \Exception
     */
    public static function format($models)
    {
        $arr = [];

        foreach ($models as $item) {
            $arr[] = [

                'sum' => round($item['amount']) . ' тг',
                'count' => $item['count']
            ];
        }
        return $arr;
    }

    public function rules()
    {
        return [
            [['dateFrom', 'dateTo'], 'safe'],
        ];
    }

    public function pagination()
    {
        $query = Transaction::find()
            ->select(['SUM(transaction.amount) as amount', 'COUNT(transaction.id) as count']);


        if ($this->dateFrom != null) {
            list($day, $month, $year) = explode(".", $this->dateFrom);
            $ymd = "$year-$month-$day";
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $query->andWhere([">=", "transaction.created_at", $ymd . ' 03:00:00']);
            } else {
                $query->andWhere([">=", "transaction.created_at", date("Y-m-d 03:00:00", strtotime('-1 day', strtotime($ymd)))]);
            }
        }

        if ($this->dateTo != null) {
            list($day, $month, $year) = explode(".", $this->dateTo);
            $ymd = "$year-$month-$day";
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $query->andWhere(["<=", "transaction.created_at", date("Y-m-d 02:59:59", strtotime('+1 day', strtotime($ymd)))]);
            } else {
                $query->andWhere(["<=", "transaction.created_at", $ymd . ' 02:59:59']);
            }
        }

        $query->asArray();

        return $query;
    }
}