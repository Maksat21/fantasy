<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 9/10/19
 * Time: 4:32 PM
 */

namespace common\models\helpers;


class DeviceVersionHelper
{
    const TYPE_ANDROID = "Android";
    const TYPE_IPHONE  = "iPhone";
    const TYPE_IPAD    = "iPad";
    const TYPE_SITE    = "Site";
    /**
     * @param String $userAgent
     * @return String
     */
    public static function getDeviceType(String $userAgent):String {
        if (strpos($userAgent, self::TYPE_ANDROID)) {
            return self::TYPE_ANDROID;
        }elseif (strpos($userAgent, self::TYPE_IPHONE)){
            return self::TYPE_IPHONE;
        }elseif (strpos($userAgent, self::TYPE_IPAD)){
            return self::TYPE_IPAD;
        }else{
            return self::TYPE_SITE;
        }
    }

    /**
     * @param String $userAgent
     * @return String
     */
    public static function getDeviceTypeByAppRequest(String $userAgent):String{
        if(self::isOkHttp($userAgent)){
            return self::TYPE_ANDROID;
        }elseif(self::isAlamofire($userAgent)){
            return self::TYPE_IPHONE;
        }
        return self::TYPE_SITE;
    }


    private static function isAlamofire($userAgent){
        return (strpos($userAgent, 'Alamofire') !== false) ? true : false;
    }

    private static function isOkHttp($userAgent){
        return (strpos($userAgent, 'okhttp') !== false) ? true : false;
    }


    public static function getUrlByDeviceType(String $deviceType):String {
        if($deviceType == self::TYPE_ANDROID){
            $url = 'https://play.google.com/store/apps/details?id=app.pillikan.kz';
        }elseif (in_array($deviceType, [self::TYPE_IPAD, self::TYPE_IPHONE])){
            $url = 'https://apps.apple.com/kz/app/pillikan/id1446448840';
        }else{
            $url = 'https://pillikan.kz/site/get-app';
        }
        return $url;
    }

    public static function getUrlTaxiByDeviceType(String $deviceType):String {
        if($deviceType == self::TYPE_ANDROID){
            $url = 'https://play.google.com/store/apps/details?id=kz.smartideagroup.driver';
        }elseif (in_array($deviceType, [self::TYPE_IPAD, self::TYPE_IPHONE])){
            $url = 'https://apps.apple.com/ru/app/pillikan-taxi/id1531073385';
        }else{
            $url = 'https://pillikan.kz/';
        }
        return $url;
    }
}