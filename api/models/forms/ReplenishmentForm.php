<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 13:51
 */

namespace api\models\forms;


use api\models\helper\FrontHelper;
use common\models\Card;
use common\models\Discount;
use common\models\Replenishment;
use yii\base\Model;

class ReplenishmentForm extends Model
{
    public $card_code;
    public $amount;
    public $discount_id;
    public $payment_method;
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function rules()
    {
        return [
            [['amount', 'card_code', 'payment_method'], 'required'],
            [['card_code', 'discount_id', 'payment_method'], 'integer'],
            [['amount'], 'number']
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $card = Card::findOne(['code' => $this->card_code]);
        if (!$card) {
            return false;
        }
        $replenishment = new Replenishment();
        $replenishment->card_id = $card->id;
        $replenishment->amount = $this->amount;

        $replenishment->user_id = $this->userId;
        $replenishment->discount_id = $this->discount_id;
        $discount = Discount::findOne(['id' => $this->discount_id]);
        if ($discount) {
            if ($discount->type == Discount::TYPE_DISCOUNT) {
                $replenishment->discount_amount = $this->amount * ($discount->quantity / 100);
            } elseif ($discount->type == Discount::TYPE_BONUS) {
                $replenishment->discount_amount = $discount->quantity;
            } else {
                return false;
            }
        } elseif ($this->discount_id == null) {
            $replenishment->discount_amount = 0;
        } else {
            return false;
        }
        $replenishment->session_id = FrontHelper::getSession($this->userId);
        $card->balance += $replenishment->amount + $replenishment->discount_amount;
        if (!$card->save()) {
            return false;
        }
        $replenishment->payment_method = $this->payment_method;
        $replenishment->type = Replenishment::TYPE_REPLENISHMENT;
        if ($replenishment->save()) {
            return $replenishment;
        } else {
            return false;
        }
    }
}