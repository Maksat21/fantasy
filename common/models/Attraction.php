<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attraction".
 *
 * @property int $id
 * @property string $name
 * @property integer $balance
 * @property integer $price
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Attraction extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['balance', 'price'], 'number'],
            [['status'], 'integer'],
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
            'name' => Yii::t('app', 'NAME'),
            'balance' => Yii::t('app', 'BALANCE'),
            'price' => Yii::t('app', 'PRICE'),
            'status' => Yii::t('news', 'STATUS'),
            'created_at' => Yii::t('main', 'CREATED_AT'),
            'updated_at' => Yii::t('main', 'UPDATED_AT'),
        ];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatusList(), $this->status);
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('main', 'STATUS_NOT_ACTIVE'),
            self::STATUS_ACTIVE => Yii::t('main', 'STATUS_ACTIVE'),
        ];
    }

    /**
     *
     */
    public function pay($count)
    {
        $this->balance += $this->price * $count;
        if ($this->save()) {
            return true;
        } else {
            return false;
        }
    }
}
