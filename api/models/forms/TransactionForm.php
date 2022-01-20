<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 14:12
 */

namespace api\models\forms;


use api\models\helper\FrontHelper;
use common\models\Terminal;
use common\models\Transaction;
use yii\base\Model;

class TransactionForm extends Model
{
    public $terminalId;
    public $cardId;
    public $amount;

    public function rules()
    {
        return [
            [['terminalId', 'cardId'], 'integer'],
            [['amount'], 'number'],
            [['terminalId', 'cardId', 'amount'], 'required'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $transaction = new Transaction();
        $transaction->terminal_id = $this->terminalId;
        $transaction->card_id = $this->cardId;
        $transaction->amount = $this->amount;
        $transaction->session_id = FrontHelper::getSession($this->userId);
        $terminal = Terminal::findOne(['id' => $this->terminalId]);
        $terminal->pay();
        if ($transaction->save()) {
            return $transaction;
        } else {
            return false;
        }
    }
}