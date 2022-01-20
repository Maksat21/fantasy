<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 12:58
 */

namespace api\models\forms;


use common\models\Transaction;
use yii\base\Model;

class TransactionPaginationForm extends Model
{
    public $page;
    public $limit;
    public $cashier;
    public $dateFrom;
    public $dateTo;

    public static function format($models)
    {
        $arr = [];
        foreach ($models as $item) {
            $arr[] = [
                'id' => trim(chunk_split($item['code'], 3, ' ')),
                'sum' => round($item['amount']) . ' тг',
                'date' => $item['created_at'] ? \Yii::$app->formatter->asDatetime($item['created_at'], 'php:H:i d.m.Y') : '',
                'attraction' => $item['name'],
                'cashier' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $item['title']),
                'session_id' => $item['session_id'],
                'action' => 'Посмотреть',
            ];
        }
        return $arr;
    }

    public function rules()
    {
        return [
            [['page', 'limit'], 'required'],
            [['page', 'limit', 'cashier'], 'integer'],
            [['dateFrom', 'dateTo', 'card'], 'safe'],
        ];
    }

    public function pagination()
    {
        $query = Transaction::find()
            ->leftJoin('card', 'card.id=transaction.card_id')
            ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
            ->leftJoin('attraction', 'attraction.id=transaction.attraction_id')
            ->orderBy(['transaction.id' => SORT_DESC])
            ->select(['transaction.id', 'card.code', 'attraction.name', 'amount', 'transaction.created_at', 'terminal.title', 'transaction.session_id'])
            ->asArray();

        if ($this->cashier != null) {
            $query->andWhere(['terminal_id' => $this->cashier]);
        }

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

        return $query;
    }
}