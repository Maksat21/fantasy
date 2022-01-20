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

class ReplenishmentExcelForm extends Model
{
    public $userId;
    public $paymentMethod;
    public $dateFrom;
    public $dateTo;

    public function rules()
    {
        return [
            [['paymentMethod', 'userId'], 'integer'],
            [['dateFrom', 'dateTo'], 'string'],
        ];
    }

    public function getString()
    {
        $string = '?';
        if ($this->userId != null) {
            $string .= '&userId=' . $this->userId;
        }

        if ($this->paymentMethod != null) {
            $string .= '&paymentMethod=' . $this->paymentMethod;
        }

        if ($this->dateFrom != null) {
            $string .= '&dateFrom=' . $this->dateFrom;
        }

        if ($this->dateTo != null) {
            $string .= '&dateTo=' . $this->dateTo;
        }

        return $string;
    }

    public function filter()
    {
        $query = Replenishment::find()
            ->orderBy(['replenishment.id' => SORT_DESC])
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->select(['replenishment.amount', 'replenishment.created_at', 'replenishment.payment_method', 'card.code', 'user.fullname', 'discount.name'])
            ->asArray();

        if ($this->userId != null) {
            $query->andWhere(['user_id' => $this->userId]);
        }

        if ($this->paymentMethod != null) {
            $query->andWhere(['payment_method' => $this->paymentMethod]);
        }

        if ($this->dateFrom != null) {
            list($day, $month, $year) = explode(".", $this->dateFrom);
            $ymd = "$year-$month-$day";
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $query->andWhere([">=", "replenishment.created_at", $ymd . ' 03:00:00']);
            } else {
                $query->andWhere([">=", "replenishment.created_at", date("Y-m-d 03:00:00", strtotime('-1 day', strtotime($ymd)))]);
            }
        }

        if ($this->dateTo != null) {
            list($day, $month, $year) = explode(".", $this->dateTo);
            $ymd = "$year-$month-$day";
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $query->andWhere(["<=", "replenishment.created_at", date("Y-m-d 02:59:59", strtotime('+1 day', strtotime($ymd)))]);
            } else {
                $query->andWhere(["<=", "replenishment.created_at", $ymd . ' 02:59:59']);
            }
        }

        return $query->all();
    }
}