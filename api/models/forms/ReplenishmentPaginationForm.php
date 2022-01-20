<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 12:15
 */

namespace api\models\forms;


use common\models\Replenishment;
use yii\base\Model;

class ReplenishmentPaginationForm extends Model
{
    public $page;
    public $limit;
    public $userId;
    public $paymentMethod;
    public $dateFrom;
    public $dateTo;

    /**
     * @param $models
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function format($models)
    {
        $arr = [];
        foreach ($models as $item) {
            $arr[] = [
                'id' => trim(chunk_split($item['code'], 3, ' ')),
                'sum' => round($item['amount']) . ' тг',
                'date' => $item['created_at'] ? \Yii::$app->formatter->asDatetime($item['created_at'], 'php:H:i d.m.Y') : '',
                'cashier' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $item['fullname']),
                'paymentMethod' => Replenishment::getStaticPaymentLabel($item['payment_method']),
                'status' => $item['name'],
            ];
        }
        return $arr;
    }

    public function rules()
    {
        return [
            [['page', 'limit'], 'required'],
            [['page', 'limit', 'paymentMethod', 'userId'], 'integer'],
            [['dateFrom', 'dateTo'], 'string'],
        ];
    }

    public function pagination()
    {
        $query = Replenishment::find()
            ->select(['replenishment.amount', 'replenishment.created_at', 'replenishment.payment_method', 'card.code', 'user.fullname', 'discount.name'])
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->orderBy(['replenishment.id' => SORT_DESC])
            ->asArray();

        if ($this->userId != null) {
            $query->andWhere(['user_id' => $this->userId]);
        }

        if ($this->paymentMethod != null) {
            $query->andWhere(['payment_method' => $this->paymentMethod]);
        }

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

        return $query;
    }
}