<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $terminal_id Терминал
 * @property int $card_id Карта
 * @property string $amount Цена
 * @property string $created_at Создано
 * @property string $updated_at Обновлено
 * @property int $session_id Сессия
 * @property int $attraction_id Аттракцион
 *
 * @property Card $card
 * @property Terminal $terminal
 * @property Attraction $attraction
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['terminal_id', 'card_id', 'session_id', 'attraction_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => Card::className(), 'targetAttribute' => ['card_id' => 'id']],
            [['terminal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Terminal::className(), 'targetAttribute' => ['terminal_id' => 'id']],
            [['attraction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::className(), 'targetAttribute' => ['attraction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'terminal_id' => Yii::t('main', 'TERMINAL'),
            'attraction_id' => Yii::t('main', 'ATTRACTION'),
            'card_id' => Yii::t('main', 'CARD'),
            'amount' => Yii::t('main', 'AMOUNT'),
            'created_at' => Yii::t('main', 'CREATED_AT'),
            'session_id' => Yii::t('main', 'SESSION_ID'),
            'updated_at' => Yii::t('main', 'UPDATED_AT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'card_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerminal()
    {
        return $this->hasOne(Terminal::className(), ['id' => 'terminal_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttraction()
    {
        return $this->hasOne(Attraction::className(), ['id' => 'attraction_id']);
    }

    /**
     * @param $userId
     * @param $cardId
     * @param $terminalPrice
     * @param $sessionId
     * @param int $count
     * @param $attractionId
     * @return bool
     */
    public static function createTransation($userId, $cardId, $terminalPrice, $sessionId, $count = 1, $attractionId)
    {
        for ($i = 0; $i < $count; $i++) {
            $transaction = new Transaction();
            $transaction->terminal_id = $userId;
            $transaction->card_id = $cardId;
            $transaction->amount = $terminalPrice;
            $transaction->session_id = $sessionId;
            $transaction->attraction_id = $attractionId;
            if (!$transaction->save()) {
                return false;
            }
        }
        return true;
    }
}
