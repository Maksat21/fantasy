<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "discount".
 *
 * @property int $id
 * @property string $name Название
 * @property int $type Тип
 * @property int $quantity Количество
 */
class Discount extends \yii\db\ActiveRecord
{
    const TYPE_DISCOUNT = 1;
    const TYPE_BONUS = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'quantity'], 'required'],
            [['type', 'quantity'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'quantity' => Yii::t('app', 'Quantity'),
        ];
    }
}
