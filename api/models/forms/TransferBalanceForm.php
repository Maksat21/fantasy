<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 11:41
 */

namespace api\models\forms;


use common\models\Card;
use common\models\Transfer;
use yii\base\Model;

class TransferBalanceForm extends Model
{
    public $cardFromId;
    public $cardToId;
    public $userId;
    public $reason;

    public function __construct($userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function rules()
    {
        return [
            [['userId', 'reason'], 'integer'],
            [['cardFromId', 'cardToId'], 'string'],
            [['cardFromId', 'cardToId', 'userId', 'reason'], 'required'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $transfer = new Transfer();
        $card_from = Card::findOne(['code' => $this->cardFromId]);
        $card_to = Card::findOne(['code' => $this->cardToId]);
        if ((!$card_from || !$card_to) || $this->cardToId == $this->cardFromId) {
            return false;
        }
        $transfer->card_from_id = $card_from->id;
        $transfer->card_to_id = $card_to->id;
        $transfer->user_id = $this->userId;
        $transfer->reason = $this->reason;
        $transfer->amount = $card_from->balance;
        (int)$card_to->balance += (int)$card_from->balance;
        $card_from->balance = 0;
        $card_from->save();
        $card_to->save();
        if ($transfer->save()) {
            return $transfer;
        } else {
            return false;
        }
    }
}