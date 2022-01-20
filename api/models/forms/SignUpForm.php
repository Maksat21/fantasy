<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.02.18
 * Time: 17:01
 */

namespace api\models\forms;


//use common\models\MProfile;
use common\models\mUser;
use common\models\SmsReport;
use Yii;
use yii\base\Model;

class SignUpForm extends Model
{
    public $iin;
    public $phone;
    public $password;
    public $password2;
    public $deviceType;
    public $ftoken;
    public $accessToken;
    public $fio;
    public function rules()
    {
        return [
            [['iin', 'phone', 'ftoken', 'password', 'password2'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'password2', 'message' => 'Пароли не совпадают'],
            ['iin', 'unique', 'targetClass' => '\common\models\mUser', 'message' => Yii::t('api','This iin has already been taken.')],
            [['iin', 'deviceType'], 'string', 'min' => 2, 'max' => 255],
        ];
    }

    /**
     * @return mUser
     * @throws \yii\base\Exception
     */
    public function signUp()
    {
        $user = new mUser();
        $user->iin = $this->iin;
        $user->phone = $this->phone;
        //        $user->device_type = $this->deviceType;
        $user->status = mUser::STATUS_ACTIVE;
        $user->f_token = $this->ftoken;
        $user->access_token = $this->accessToken;
        $user->fio = 'Не мое ФИО';
        $user->setPassword($this->password);
        $user->save();
        return $user;
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