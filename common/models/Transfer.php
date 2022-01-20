<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transfer".
 *
 * @property int $id
 * @property int $card_from_id
 * @property int $card_to_id
 * @property string $amount
 * @property int $user_id
 * @property int $reason
 * @property string $created_at
 * @property string $updated_at
 */
class Transfer extends \yii\db\ActiveRecord
{
    const REASON_CARD_DEFECT = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['card_from_id', 'card_to_id', 'reason'], 'required'],
            [['card_from_id', 'card_to_id', 'user_id', 'reason'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
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
            'id' => Yii::t('app', 'ID'),
            'card_from_id' => Yii::t('app', 'Card From ID'),
            'card_to_id' => Yii::t('app', 'Card To ID'),
            'amount' => Yii::t('app', 'Amount'),
            'user_id' => Yii::t('app', 'User ID'),
            'reason' => Yii::t('app', 'Reason'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
