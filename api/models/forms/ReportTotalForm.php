<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 15:24
 */

namespace api\models\forms;


use common\models\Attraction;
use common\models\Card;
use common\models\Replenishment;
use common\models\Transaction;
use yii\base\Model;

class ReportTotalForm extends Model
{

    public $dateFrom;
    public $dateTo;
    public $paymentMethod;


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
                'paymentMethod' => Replenishment::getStaticPaymentLabel($item['payment_method']),
                'count' => $item['count']
            ];
        }
        return $arr;
    }

    public function rules()
    {
        return [
            [['paymentMethod'], 'integer'],
            [['dateFrom', 'dateTo'], 'safe'],
        ];
    }

    public function pagination()
    {
        $query = Replenishment::find()
            ->orderBy(['replenishment.id' => SORT_DESC])
            ->select(['SUM(replenishment.amount) as amount', 'replenishment.type', 'replenishment.created_at', 'replenishment.payment_method', 'COUNT(replenishment.id) as count'])
            ->where(['replenishment.type' => 2]);


        if ($this->dateFrom != null) {
            $dateFrom = strtok($this->dateFrom, ' ');
            $timeFrom = strtok(' ');
            list($day, $month, $year) = explode(".", $dateFrom);
            $ymd = "$year-$month-$day";
            if ($timeFrom) {
                $query->andWhere(['>=', 'replenishment.created_at', $ymd . ' ' . $timeFrom . ':00']);
            } else {
                $query->andWhere(['>=', 'replenishment.created_at', $ymd . ' 00:00:00']);
            }
        }

        if ($this->dateTo != null) {
            $dateTo = strtok($this->dateTo, ' ');
            $timeTo = strtok(' ');
            list($day, $month, $year) = explode(".", $dateTo);
            $ymd = "$year-$month-$day";
            if ($timeTo) {
                $query->andWhere(['<=', 'replenishment.created_at', $ymd . ' ' . $timeTo . ':59']);
            } else {
                $query->andWhere(['<=', 'replenishment.created_at', $ymd . ' 23:59:59']);
            }
        }

        if ($this->paymentMethod != null) {
            $query->andWhere(['payment_method' => $this->paymentMethod]);
        }

        $query->groupBy('replenishment.payment_method')->asArray();

        return $query;
    }
}