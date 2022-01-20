<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 24.03.2021
 * Time: 17:24
 */

namespace api\models\forms;


use common\models\Card;
use yii\base\Model;

class BlockCardForm extends Model
{
    public $id;
    public $status;

    /**
     * @return array
     */
    public function rules()
    {

        return [
            [['id', 'status'], 'required'],
            [['id', 'status'], 'integer'],
        ];
    }

    /**
     * @return bool|Card
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $card = Card::findOne(['id' => $this->id]);
        if (!$card) {
            return false;
        }
        if ($this->status == 0) {
            $card->status = Card::STATUS_BANNED;
        } else {
            $card->status = Card::STATUS_ACTIVE;
        }
        if ($card->save()) {
            return $card;
        } else {
            return false;
        }
    }
}