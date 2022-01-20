<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.02.2021
 * Time: 17:35
 */

namespace common\models\helpers;


class CurrencyHelper
{
    //запрос списка валют
    public static function getCurrencyList()
    {
        return self::query();
    }

    private static function query()
    {
        $json = \Yii::$app->params['currency']['url'];
        $json = file_get_contents($json);
        $json = simplexml_load_string($json);
        $json = json_encode($json);
        $json = json_decode($json, true);
        return $json;
    }

    //курс тенге к доллару США
    public static function getCurrencyUsd()
    {
        $query = self::query();
        return $query['channel']['item']['10'];
    }

    //курс тенге к евро
    public static function getCurrencyEur()
    {
        $query = self::query();
        return $query['channel']['item']['11'];
    }

    //курс тенге к рублю
    public static function getCurrencyRub()
    {
        $query = self::query();
        return $query['channel']['item']['24'];
    }

    //курс тенге к любой валюте
    public static function getCurrency($currency)
    {
        $query = self::query();
        foreach ($query['channel']['item'] as $q) {
            if ($q['title'] == $currency) {
                return $q;
            }
        }
        return "Нет такой валюты";
    }
}