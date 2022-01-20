<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 15:24
 */

namespace api\models\forms;


use common\models\Attraction;
use yii\base\Model;

class AttractionPaginationForm extends Model
{
    public $page;
    public $limit;
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
                'name' => $item['name'],
                'count' => $item['count'],
            ];
        }
        return $arr;
    }

    public function rules()
    {
        return [
            [['page', 'limit'], 'required'],
            [['page', 'limit'], 'integer'],
            [['dateFrom', 'dateTo'], 'safe'],
        ];
    }

    public function pagination()
    {
        $query = Attraction::find()
            ->leftJoin('transaction', 'transaction.attraction_id=attraction.id')
            ->select(['SUM(transaction.amount) as amount', 'attraction.name', 'COUNT(transaction.id) as count']);

        if ($this->dateFrom != null) {
            $dateFrom = strtok($this->dateFrom, ' ');
            $timeFrom = strtok(' ');
            list($day, $month, $year) = explode(".", $dateFrom);
            $ymd = "$year-$month-$day";
            if ($timeFrom) {
                $query->andWhere(['>=', 'transaction.created_at', $ymd . ' ' . $timeFrom . ':00']);
            } else {
                $query->andWhere(['>=', 'transaction.created_at', $ymd . ' 00:00:00']);
            }
        }

        if ($this->dateTo != null) {
            $dateTo = strtok($this->dateTo, ' ');
            $timeTo = strtok(' ');
            list($day, $month, $year) = explode(".", $dateTo);
            $ymd = "$year-$month-$day";
            if ($timeTo) {
                $query->andWhere(['<=', 'transaction.created_at', $ymd . ' ' . $timeTo . ':59']);
            } else {
                $query->andWhere(['<=', 'transaction.created_at', $ymd . ' 23:59:59']);
            }
        }

        $query->groupBy('attraction.id')->asArray()
            ->all();

        return $query;
    }
}