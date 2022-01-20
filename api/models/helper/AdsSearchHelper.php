<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 08.07.2018
 * Time: 12:43
 */

namespace api\models\helper;


use common\models\Ads;
use common\models\helpers\AdsConstants;
use common\models\MProfile;

class AdsSearchHelper
{
    public static function createQuery(MProfile $profile){
        $date = date('Y-m-d', time());

        $sexWhere = ($profile->sex == AdsConstants::SEX_MALE) ? 'male = 1' : 'female = 1';
        $catWhere = self::getCategoryQuery($profile);
        $ageWhere = self::getAgeQuery($profile->birthday);
        $sqlWhere = 'status = ' .AdsConstants::STATUS_PUBLISHED .
            ' and (region_id = '.$profile->region_id . ' || region_id = 1)'.
            ' and view_count > sent'.
            ' and (city_id = 0 || city_id = '.$profile->city_id.' || city_id = 1)'.
            ' and '.$sexWhere.
            ' and '.$catWhere.
            " and date_start <= '".$date."'".
            ' and '.$ageWhere ;
        return $sqlWhere;
    }


    private static function getCategoryQuery(MProfile $profile){
        $catWhere = '';
        if($profile->cat_1){
            $catWhere .= ' cat_1 = 1';
        }
        if($profile->cat_2){
            $catWhere .= (strlen($catWhere)) ? ' or cat_2 = 1' : ' cat_2 = 1';
        }
        if($profile->cat_3){
            $catWhere .= (strlen($catWhere)) ? ' or cat_3 = 1' : ' cat_3 = 1';
        }
        if($profile->cat_4){
            $catWhere .= (strlen($catWhere)) ? ' or cat_4 = 1' : ' cat_4 = 1';
        }
        if($profile->cat_5){
            $catWhere .= (strlen($catWhere)) ? ' or cat_5 = 1' : ' cat_5 = 1';
        }
        if($profile->cat_6){
            $catWhere .= (strlen($catWhere)) ? ' or cat_6 = 1' : ' cat_6 = 1';
        }
        if($profile->cat_7){
            $catWhere .= (strlen($catWhere)) ? ' or cat_7 = 1' : ' cat_7 = 1';
        }
        if($profile->cat_8){
            $catWhere .= (strlen($catWhere)) ? ' or cat_8 = 1' : ' cat_8 = 1';
        }
        if($profile->cat_9){
            $catWhere .= (strlen($catWhere)) ? ' or cat_9 = 1' : ' cat_9 = 1';
        }
        if($profile->cat_10){
            $catWhere .= (strlen($catWhere)) ? ' or cat_10 = 1' : ' cat_10 = 1';
        }
        if($profile->cat_11){
            $catWhere .= (strlen($catWhere)) ? ' or cat_11 = 1' : ' cat_11 = 1';
        }
        if($profile->cat_12){
            $catWhere .= (strlen($catWhere)) ? ' or cat_12 = 1' : ' cat_12 = 1';
        }
        if(strlen($catWhere)){
            $catWhere = ' ('.$catWhere.')';
        }
        return $catWhere;
    }

    private static function getAgeQuery($birthday){
        $birthdayDate = substr($birthday, 0, 4);
        $year = date('Y');
        $userAge = $year - $birthdayDate;
        $ageWhere = '(age_from < '. ($userAge-1) . ' AND age_to > ' . ($userAge+1) . ')';
        return $ageWhere;
    }

}