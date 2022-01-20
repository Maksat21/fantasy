<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $img
 * @property int $status
 * @property string $slug
 * @property string $meta_description
 * @property string $meta_keywords
 * @property int $created_at
 * @property int $updated_at
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }


    public function behaviors()
    {
        return [
            'slug' => [
                'class' => 'Zelenin\yii\behaviors\Slug',
                'slugAttribute' => 'slug',
                'attribute' => ['id', 'title'],
                // optional params
                'ensureUnique' => true,
                'replacement' => '-',
                'lowercase' => true,
                'immutable' => false,
                'transliterateOptions' => 'Russian-Latin/BGN; Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC;'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'required'],
            [['title', 'img', 'slug', 'meta_description', 'meta_keywords'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('news', 'ID'),
            'title' => Yii::t('news', 'Title'),
            'content' => Yii::t('news', 'Content'),
            'img' => Yii::t('news', 'Img'),
            'status' => Yii::t('news', 'Status'),
            'slug' => Yii::t('news', 'Slug'),
            'meta_description' => Yii::t('news', 'Meta Description'),
            'meta_keywords' => Yii::t('news', 'Meta Keywords'),
            'created_at' => Yii::t('news', 'Created At'),
            'updated_at' => Yii::t('news', 'Updated At'),
        ];
    }
}
