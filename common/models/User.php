<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $fullname
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $access_token
 * @property string $f_token
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_NOT_ACTIVE = 9;

    const ROLE_ADMIN = 1;
    const ROLE_CASHIER = 2;
    const ROLE_ACCOUNTANT = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_NOT_ACTIVE]],
            [['access_token', 'f_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getStatusLabel($status)
    {
        return ArrayHelper::getValue(static::getStatusList(), $status, Yii::t('main', 'NOT_SET'));
    }

    /**
     * @param $role
     * @return mixed
     * @throws \Exception
     */
    public static function getRoleLabel($role)
    {
        return ArrayHelper::getValue(static::getRoles(), $role, Yii::t('main', 'NOT_SET'));
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('main', 'STATUS_NOT_ACTIVE'),
            self::STATUS_ACTIVE => Yii::t('main', 'STATUS_ACTIVE'),
            self::STATUS_DELETED => Yii::t('main', 'STATUS_DELETED'),
        ];
    }

    /**
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => Yii::t('main', 'ROLE ADMIN'),
            self::ROLE_CASHIER => Yii::t('main', 'ROLE CASHIER'),
            self::ROLE_ACCOUNTANT => Yii::t('main', 'ROLE ACCOUNTANT'),
            ];
    }

    /**
     * Поиск пользователя по логину
     * @param $login
     * @return User|null
     */
    public static function findByLogin($login)
    {
        return static::findOne(['username' => $login, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'id' => $this->id,
            'login' => $this->username,
            'fullname' => $this->fullname,
            'role' => $this->role,
            'access_token' => $this->access_token,
        ];
    }
}
