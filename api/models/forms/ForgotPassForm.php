<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.02.18
 * Time: 17:01
 */

namespace api\models\forms;


use common\models\mUser;
use yii\base\Model;
use yii\web\ConflictHttpException;

class ForgotPassForm extends Model
{
    public $_user;

    public $iin;
    public $phone;
    public $password;
    public $password2;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['iin', 'phone', 'password', 'password2'], 'string'],
            [['iin', 'phone', 'password', 'password2'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'password2', 'message' => 'Пароли не совпадают'],
        ];
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

        if($this->_user && $this->_user->status == mUser::STATUS_INACTIVE){
            throw new ConflictHttpException(\Yii::t('api', 'User is not activated'));
        }
        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'iin' => 'ИИН',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'password2' => 'Пароль 2'
        ];
    }
}