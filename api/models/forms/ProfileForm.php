<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 02.03.2018
 * Time: 15:41
 */

namespace api\models\forms;


use common\models\Badge;
use common\models\Cities;
use common\models\helpers\DeviceVersionHelper;
use common\models\MProfile;
use common\models\MUser;
use common\models\UserBans;
use common\models\VolunteerLinks;
use common\models\Volunteers;
use yii\base\Model;
use yii\web\ConflictHttpException;

class ProfileForm extends Model
{
    public $userId;
    public $fio;
    public $phone;
    public function rules()
    {
        return [
            [['userId','fio'], 'required'],
            [['fio', 'phone'], 'string', 'min' => 2, 'max' => 255],
        ];
    }

    public function save(){
        if(!$this->validate()){
            return false;
        }
        $user = mUser::findOne($this->userId);
        $user->fio = $this->fio;
//        $user->phone = $this->phone;
        if($user->save()) {
            return $user;
        } else {
            return false;
        }
    }
//
//    private function createBadge(){
//        $badge = new Badge();
//        $badge->user_id = (int)$this->userId;
//        $badge->save();
//    }
}