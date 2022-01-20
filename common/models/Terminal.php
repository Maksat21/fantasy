<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "terminal".
 *
 * @property int $id
 * @property string $title Название
 * @property string $login Логин
 * @property string $access_token Access токен
 * @property string $password_hash Пароль
 * @property string $f_token FireBase токен
 * @property int $os_type Тип ОС
 * @property string $app_ver Версия приложения
 * @property int $status Статус
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 * @property string $new_password Дата обновления
 * @property integer $attraction_id Аттракицон
 * @property integer $role Роль
 */
class Terminal extends \yii\db\ActiveRecord
{
    // STATUS
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    // OS TYPE
    const OS_TYPE_ANDROID = 1;
    const OS_TYPE_IOS     = 2;

    // ROLE
    const ROLE_OPERATOR = 1;
    const ROLE_ADMIN = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terminal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['access_token'], 'string'],
            [['os_type', 'status', 'role', 'attraction_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['access_token'], 'unique'],
            [['title', 'login', 'password_hash', 'f_token', 'app_ver'], 'string', 'max' => 255],
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
            'title' => Yii::t('main', 'TITLE'),
            'login' => Yii::t('main', 'LOGIN'),
            'access_token' => Yii::t('main', 'ACCESS_TOKEN'),
            'password_hash' => Yii::t('main', 'PASSWORD_HASH'),
            'f_token' => Yii::t('main', 'F_TOKEN'),
            'os_type' => Yii::t('main', 'OS_TYPE'),
            'app_ver' => Yii::t('main', 'APP_VER'),
            'status' => Yii::t('main', 'STATUS'),
            'created_at' => Yii::t('main', 'CREATED_AT'),
            'updated_at' => Yii::t('main', 'UPDATED_AT'),
            'new_password' => Yii::t('main', 'NEW_PASSWORD'),
            'attraction_id' => Yii::t('main', 'ATTRACTION ID'),
            'role' => Yii::t('main', 'ROLE'),
        ];
    }

    /**
     * Установка пароля
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
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
     * Проверка пароля
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
//        print_r($password); die;
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Поиск пользователя по логину
     * @param $login
     * @return Terminal|null
     */
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'login' => $this->login,
            'role' => $this->role,
            'access_token' => $this->access_token
        ];
    }

}
