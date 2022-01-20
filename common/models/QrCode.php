<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "barcode".
 *
 * @property int $id
 * @property string $uuid
 * @property string $path
 */
class QrCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qr_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uuid' => Yii::t('app', 'Uuid'),
            'path' => Yii::t('app', 'Path'),
        ];
    }

    /**
     * @return string
     */
    public function getQrPath()
    {
        return Yii::$app->params['staticDomain'] . 'qr' . $this->path;
    }
}
