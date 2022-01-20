<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 13:47
 */

namespace api\models\forms;


use common\models\Card;
use yii\base\Model;

class CardForm extends Model
{
    public $code;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
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
        $card = new Card();
        $card->code = $this->code;
        if ($card->save()) {
            return $card;
        } else {
            return false;
        }
    }
}