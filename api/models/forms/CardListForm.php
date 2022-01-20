<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 07.05.2021
 * Time: 10:45
 */

namespace api\models\forms;


use common\models\Card;
use common\models\Replenishment;
use common\models\Transaction;
use Yii;
use yii\base\Model;
use yii\web\ConflictHttpException;

class CardListForm extends Model
{
    public $page;
    public $limit;
    public $card;

    public function rules()
    {
        return [
            [['page', 'limit'], 'integer'],
            [['page', 'limit'], 'required'],
            [['card'], 'string'],
        ];
    }

//    public function pagination()
//    {
//
//            $cards = Card::find()
//                ->select(['id', 'code', 'balance', 'status', 'created_at']);
//
//            if ($this->card != null) {
//                $cards->andWhere(['code' => $this->card]);
//            }
//            $cards = $cards->all();
//            if($cards) {
//                foreach ($cards as $l) {
//                    $l->created_at = Yii::$app->formatter->asDatetime($l->created_at, 'php:H:i d.m.Y');
//                }
//            }
//
//        return $cards;
//    }

}