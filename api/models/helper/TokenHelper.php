<?php
namespace api\models\helper;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use UnexpectedValueException;
use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 26.02.18
 * Time: 16:21
 */

class TokenHelper
{
    public static function createToken($userId, $userAgent, $deviceType){
        $url = '';
        $currentTime = time();
        $token = [
            'iss' => $url,
            'aud' => $url,
            'iat' => $currentTime,
            'nbf' => $currentTime + 1,
            'exp' => strtotime('+365 day', $currentTime),
            'data' => [
                'userId' => $userId,
                'userAgent' => $userAgent,
                'deviceType'=> $deviceType
            ]
        ];
//        $refreshToken = $token;
//        $refreshToken['exp'] = $expiredTime;
        JWT::$leeway += 520;
        $tokens['access_token'] = JWT::encode($token, base64_encode(Yii::$app->params['jwtKey']), Yii::$app->params['jwtAlg']);
//        $tokens['refresh_token'] = JWT::encode($refreshToken, base64_encode(Yii::$app->params['jwtKey']), Yii::$app->params['jwtAlg']);
        $expiredTime = strtotime('+356 day', $currentTime);
        $tokens['expired_time'] = $expiredTime;
        return $tokens;
    }

    public static function getToken($headers){
        $token = false;
        if(isset($headers['authorization'])) {
            list($token) = sscanf($headers['authorization'], 'Bearer %s');
        }
        return $token;
    }

    public static function decodeTokenByHeader($headers){
        $token = self::getToken($headers);
        return self::decodeToken($token);
    }

    public static function decodeToken($token){
        try{
            $data = JWT::decode($token,  base64_encode(Yii::$app->params['jwtKey']), [\Yii::$app->params['jwtAlg']]);
        }catch (ExpiredException | UnexpectedValueException $e){
            throw new UnauthorizedHttpException("Данные авторизации устарели");
        }
        return $data;
    }

    public static function getUserId($headers){
        $userData = self::decodeTokenByHeader($headers);
        if($userData){
            return $userData->data->userId;
        }
        else {
            return false;
        }
    }

    public static function getUserIdByToken($token){
        $userData = self::decodeToken($token);
        if($userData){
            if($userData->data->userId){
                return $userData->data->userId;
            }
        }
        return false;
    }
}