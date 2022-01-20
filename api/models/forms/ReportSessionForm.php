<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 04.05.2021
 * Time: 11:24
 */

namespace api\models\forms;


use api\models\helper\FrontHelper;
use common\models\Card;
use common\models\Replenishment;
use yii\base\Model;

class ReportSessionForm extends Model
{
    public $page;
    public $limit;
    public $userId;
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
            [['page', 'limit', 'userId'], 'integer'],
            [['code'], 'string'],
        ];
    }

    public function pagination()
    {
        $query = Replenishment::find()
            ->leftJoin('card', 'card.id=replenishment.card_id')
            ->leftJoin('user', 'user.id=replenishment.user_id')
            ->leftJoin('discount', 'discount.id=replenishment.discount_id')
            ->select(['replenishment.amount', 'replenishment.type', 'replenishment.created_at', 'replenishment.payment_method', 'card.code', 'user.fullname', 'discount.name', 'session_id'])
            ->asArray();

        $sessionId = FrontHelper::getSession($this->userId);

        $query->andWhere(['user_id' => $this->userId])
            ->andWhere(['session_id' => $sessionId]);

        if ($this->code != null) {
            $card = Card::findOne(['code' => $this->code]);
            if ($card) {
                $query->andWhere(['card_id' => $card->id]);
            } else {
                $query->andWhere('0=1');
            }
        }

        return $query;
    }
}