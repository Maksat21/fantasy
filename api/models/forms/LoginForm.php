<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.02.18
 * Time: 17:01
 */

namespace api\models\forms;


use common\models\Terminal;
use Yii;
use yii\base\Model;
use yii\web\ConflictHttpException;

class LoginForm extends Model
{
    public $login;
    public $password;
    public $ftoken;
    public $_user;

    /**
     * @inheritdoc
     */
        public function rules()
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
            [['ftoken'], 'default', 'value' => null],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, \Yii::t('api', 'User not found'));
                return;
            }
//            $this->password = Yii::$app->security->generatePasswordHash($this->password);
//print_r($this); die;
            if (!$user->validatePassword(trim($this->password))) {
                $this->addError($attribute, \Yii::t('api', 'Incorrect username or password.'));
            }

        }
    }

    /**
     * @return Terminal|null
     */
    private function getUser(){
        if ($this->_user === null) {
            $this->_user = Terminal::findByLogin(trim($this->login));
        }

        return $this->_user;
    }

    /**
     * @param null $attributeNames
     * @param null $clearErrors
     * @return bool
     * @throws ConflictHttpException
     */
    public function validate($attributeNames = null, $clearErrors = null)
    {
        if(!parent::validate($attributeNames, $clearErrors)){
           return false;
        }

        if($this->_user && $this->_user->status == Terminal::STATUS_NOT_ACTIVE){
            throw new ConflictHttpException(\Yii::t('api', 'User is not activated'));
        }
        return true;
    }
}