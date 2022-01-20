<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "session".
 *
 * @property int $id
 * @property int $user_id Пользователь
 * @property string $session_start Начало сессии
 * @property string $session_end Конец сессии
 * @property int $transaction_count Количество транзакций
 * @property string $transaction_amount Сумма транзакций
 */
class Session extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'transaction_count'], 'integer'],
            [['session_start', 'session_end'], 'safe'],
            [['transaction_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'session_start' => Yii::t('app', 'Session Start'),
            'session_end' => Yii::t('app', 'Session End'),
            'transaction_count' => Yii::t('app', 'Transaction Count'),
            'transaction_amount' => Yii::t('app', 'Transaction Amount'),
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
                'updatedAtAttribute' => false,
                'createdAtAttribute' => 'session_start',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                }
            ]
        ];
    }
}
