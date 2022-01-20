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

class ReportPaginationForm extends Model
{
    public $page;
    public $limit;
    public $sessionId;
    public $card;
    public $dateFrom;
    public $dateTo;
    public $paymentMethod;
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

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
                'id' => trim(chunk_split($item['code'], 3, ' ')),
                'sum' => round($item['amount']) . ' тг',
                'date' => $item['created_at'] ? $item['created_at'] : '',
//                'date' => $item['created_at'] ? \Yii::$app->formatter->asDatetime($item['created_at'], 'php:H:i d.m.Y') : '', // Не верное время что то с таимзоной
                'cashier' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $item['fullname']),
                'paymentMethod' => Replenishment::getStaticPaymentLabel($item['payment_method']),
                'status' => $item['name'],
                'discount_amount' => $item['discount_amount'],
                'action' => "Посмотреть",
            ];
        }
        return $arr;
    }

    public function rules()
    {
        return [
            [['page', 'limit'], 'required'],
            [['page', 'limit', 'sessionId', 'paymentMethod'], 'integer'],
            [['dateFrom', 'dateTo', 'card'], 'safe'],
        ];
    }

    public function pagination()
    {
        $query = Replenishment::find()
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->orderBy(['replenishment.id' => SORT_DESC])
            ->select(['replenishment.amount', 'replenishment.type', 'replenishment.created_at', 'replenishment.payment_method', 'card.code', 'user.fullname', 'discount.name', 'replenishment.discount_amount'])
            ->asArray();

        $user = User::findOne(['id' => $this->userId]);
        if (!$user || $user->role != User::ROLE_ADMIN) {
            $query->andWhere(['replenishment.user_id' => $this->userId]);
        }

        if ($this->sessionId) {
            $query->andFilterWhere(['session_id' => $this->sessionId]);
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

        if ($this->card != null) {
            $card = Card::findOne(['code' => $this->card]);
            if ($card) {
                $query->andWhere(['card_id' => $card->id]);
            } else {
                $query->andWhere('0=1');
            }
        }

        return $query;
    }
}