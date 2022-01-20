<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "card".
 *
 * @property int $id
 * @property string $code Код карты
 * @property string $balance Баланс
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class Card extends \yii\db\ActiveRecord
{
    const STATUS_BANNED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
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
            'code' => Yii::t('main', 'CODE'),
            'balance' => Yii::t('main', 'BALANCE'),
            'created_at' => Yii::t('main', 'CREATED_AT'),
            'updated_at' => Yii::t('main', 'UPDATED_AT'),
            'status' => Yii::t('main', 'STATUS'),
        ];
    }

    /**
     * @param $amount
     * @return bool
     */
    public function pay($amount)
    {
//        print_r($amount); die;
        if ($this->balance < $amount || ($amount == 0 && $this->balance == 0)) {
            return false;
        } else {
            $this->balance -= $amount;
            if ($this->save()) {
                return true;
            } else {
                return false;
            }
        }
    }
}
