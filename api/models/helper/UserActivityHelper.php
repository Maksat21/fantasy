<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 08.06.2018
 * Time: 10:26
 */

namespace api\models\helper;


use common\models\MProfile;
use common\models\UserActivity;

class UserActivityHelper
{
    public static function save($userId){
        $profile = MProfile::findOne(['user_id' => $userId]);
        if(!$profile){
            return false;
        }
        $date = date('Y-m-d', time());

        $userActivity = UserActivity::findOne(['current_date' => $date, 'region_id' => $profile->region_id]);
        if($userActivity){
            $userActivity->view_count = $userActivity->view_count + 1;
        }else{
            $userActivity = new UserActivity();
            $userActivity->current_date = $date;
            $userActivity->region_id = $profile->region_id;
        }
        $userActivity->save();
    }
}