<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 15:24
 */

namespace api\models\forms;


use common\models\Card;
use common\models\Replenishment;
use common\models\User;
use yii\base\Model;

class ReportPaginationCashierForm extends Model
{
    public $page;
    public $limit;
    public $userId;
    public $paymentMethod;
    public $dateFrom;
    public $dateTo;
    public $code;

    public function __construct($userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

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
            [['page', 'limit', 'userId'], 'required'],
            [['page', 'limit', 'paymentMethod', 'userId'], 'integer'],
            [['dateFrom', 'dateTo', 'code'], 'string'],
        ];
    }

    public function pagination()
    {
        $query = Replenishment::find()
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->select(['replenishment.amount', 'replenishment.type', 'replenishment.created_at', 'replenishment.payment_method', 'card.code', 'user.fullname', 'discount.name'])
            ->asArray();

        $user = User::findOne(['id' => $this->userId]);
        if (!$user || $user->role != User::ROLE_ADMIN) {
            $query->andWhere(['replenishment.user_id' => $this->userId]);
        }

        if ($this->paymentMethod != null) {
            $query->andWhere(['payment_method' => $this->paymentMethod]);
        }

        if ($this->code != null) {
            $card = Card::findOne(['code' => $this->code]);
            if ($card) {
                $query->andWhere(['card_id' => $card->id]);
            } else {
                $query->andWhere('0=1');
            }
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