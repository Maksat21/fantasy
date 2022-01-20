<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "replenishment".
 *
 * @property int $id
 * @property int $card_id Карта
 * @property string $amount Сумма
 * @property int $user_id Оператор
 * @property int $discount_id
 * @property string $discount_amount
 * @property int $payment_method
 * @property string $created_at Создано
 * @property string $updated_at Обновлено
 * @property string $type Тип
 * @property int $session_id
 *
 * @property Card $card
 * @property User $user
 */
class Replenishment extends \yii\db\ActiveRecord
{
    const PAYMENT_BY_CARD = 1;
    const PAYMENT_BY_CASH = 2;
    const PAYMENT_BY_KASPI = 3;

    const TYPE_ACTIVATE = 1;
    const TYPE_REPLENISHMENT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'replenishment';
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getStaticPaymentLabel($payment_method)
    {
        return ArrayHelper::getValue(static::getPaymentList(), $payment_method);
    }

    /**
     * @return array
     */
    public static function getPaymentList()
    {
        return [
            self::PAYMENT_BY_CARD => Yii::t('main', 'PAYMENT BY CARD'),
            self::PAYMENT_BY_CASH => Yii::t('main', 'PAYMENT BY CASH'),
            self::PAYMENT_BY_KASPI => Yii::t('main', 'PAYMENT BY KASPI'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['card_id', 'user_id', 'discount_id', 'payment_method', 'type', 'session_id'], 'integer'],
            [['amount', 'discount_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => Card::className(), 'targetAttribute' => ['card_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Session::className(), 'targetAttribute' => ['session_id' => 'id']],
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
            'card_id' => Yii::t('main', 'CARD'),
            'amount' => Yii::t('main', 'AMOUNT'),
            'user_id' => Yii::t('main', 'USER'),
            'created_at' => Yii::t('main', 'CREATED_AT'),
            'updated_at' => Yii::t('main', 'UPDATED_AT'),
            'type' => Yii::t('main', 'TYPE'),
            'session_id' => Yii::t('main', 'SESSION_ID'),
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::className(), ['id' => 'session_id']);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentLabel()
    {
        return ArrayHelper::getValue(static::getPaymentList(), $this->payment_method);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getTypeLabel()
    {
        return ArrayHelper::getValue(static::getTypeList(), $this->type);
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_ACTIVATE => Yii::t('main', 'TYPE ACTIVATE'),
            self::TYPE_REPLENISHMENT => Yii::t('main', 'TYPE REPLENISHMENT'),
        ];
    }
}