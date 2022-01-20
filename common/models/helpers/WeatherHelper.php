<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.02.2021
 * Time: 17:05
 */

namespace common\models\helpers;

use Yii;

//coord
//  coord.lon долгота
//  coord.lat щирота
//weather
//  weather.id Идентификатор погодных условий
//  weather.main Группа погодных параметров (Дождь, Снег, Экстремальные и др.)
//  weather.description Погодные условия внутри группы.
//  weather.icon Идентификатор значка погоды
//base Внутренний параметр
//main
//  main.temp Температура. Единица измерения по умолчанию: Кельвин, Метрическая: Цельсий, Имперская: Фаренгейт.
//  main.feels_like Этот температурный параметр определяет восприятие человеком погоды.
//  main.pressure Атмосферное давление (на уровне моря, если нет данных sea_level или grnd_level), ГПа
//  main.humidity Влажность, %
//  main.temp_min Минимальная температура на данный момент. Это минимальная наблюдаемая в настоящее время температура (в пределах крупных мегаполисов и городских районов)
//  main.temp_max Максимальная температура на данный момент. Это максимальная наблюдаемая в настоящее время температура (в пределах крупных мегаполисов и городских районов)
//  main.sea_level Атмосферное давление на уровне моря, ГПа
//  main.grnd_level Атмосферное давление на уровне земли, ГПа
//wind
//  wind.speed Скорость ветра. Единица измерения по умолчанию: метр/сек, Метрическая: метр/сек, Имперская: мили/час.
//  wind.deg Направление ветра, градусы (метеорологические)
//  wind.gust Порыв ветра
//clouds
//  clouds.all Облачность, %
//rain
//  rain.1h Объем дождя за последний 1 час, мм
//  rain.3h Объем дождя за последние 3 часа, мм
//snow
//  snow.1h Объем снега за последний 1 час, мм
//  snow.3h Объем снега за последние 3 часа, мм
//dt Время вычисления данных, unix, UTC
//sys
//  sys.type Внутренний параметр
//  sys.id Внутренний параметр
//  sys.message Внутренний параметр
//  sys.country Код страны (GB, JP и т. д.)
//  sys.sunrise Время восхода солнца, unix, UTC
//  sys.sunset Время захода солнца, unix, UTC
//timezone Смещение в секундах от мирового
//id Код города
//name Название города
//cod Внутренний параметр

//https://openweathermap.org/current
class WeatherHelper
{
    const SHYMKENT_ID = 1518980;

    //q	название города
    //mode вывод в виде xml, html, json
    //units Система по фаренгейту (imperial), по цельсию (metric), по кельвину (standard)
    //lang Язык
    public static function byCityName($q, $lang = null, $mode = null, $units = null)
    {
        $params = [
            'q' => $q,
            'lang' => $lang,
            'mode' => $mode,
            'units' => $units,
        ];
        return self::query($params);
    }

    private static function query($params)
    {
        $queryParams = '';
        foreach ($params as $key => $value) {
            if ($value != null) {
                $queryParams = $queryParams . '&' . $key . '=' . $value;
            }
        }
        $json = \Yii::$app->params['weather']['url'] . '?appid=' . Yii::$app->params['weather']['api_key'] . $queryParams;
        $json = file_get_contents($json);
        if ($params['mode'] == 'json' || $params['mode'] == null) {
            $json = json_decode($json);
        }
        return $json;
    }

    //id айди города. Список скачать тут http://bulk.openweathermap.org/sample/
    //mode вывод в виде xml, html, json
    //units Система по фаренгейту (imperial), по цельсию (metric), по кельвину (standard)
    //lang Язык
    public static function byCityId($id, $lang = null, $mode = null, $units = null)
    {
        $params = [
            'id' => $id,
            'lang' => $lang,
            'mode' => $mode,
            'units' => $units,
        ];
        return self::query($params);
    }

    //lat, lon координаты места
    //mode вывод в виде xml, html, json
    //units Система по фаренгейту (imperial), по цельсию (metric), по кельвину (standard)
    //lang Язык
    public static function byCoordinates($lat, $lon, $lang = null, $mode = null, $units = null)
    {
        $params = [
            'lat' => $lat,
            'lon' => $lon,
            'lang' => $lang,
            'mode' => $mode,
            'units' => $units,
        ];
        return self::query($params);
    }

    //zip Zip код
    //mode вывод в виде xml, html, json
    //units Система по фаренгейту (imperial), по цельсию (metric), по кельвину (standard)
    //lang Язык
    public static function byZipCode($zip, $lang = null, $mode = null, $units = null)
    {
        $params = [
            'zip' => $zip,
            'lang' => $lang,
            'mode' => $mode,
            'units' => $units,
        ];
        return self::query($params);
    }

    //bbox Ограничивающая коробка в виде 'lon-left,lat-bottom,lon-right,lat-top,zoom'
    //units Система по фаренгейту (imperial), по цельсию (metric), по кельвину (standard)
    //lang Язык
    public static function byBbox($bbox, $lang = null, $mode = null, $units = null)
    {
        $params = [
            'bbox' => $bbox,
            'lang' => $lang,
            'mode' => $mode,
            'units' => $units,
        ];
        return self::query($params);
    }

    //lat, lon координаты точки
    //cnt количество городов вокруг точки. Максимум 50
    //mode вывод в виде xml, html, json
    //units Система по фаренгейту (imperial), по цельсию (metric), по кельвину (standard)
    //lang Язык
    public static function circle($lat, $lon, $cnt = null, $lang = null, $mode = null, $units = null)
    {
        $params = [
            'lat' => $lat,
            'lon' => $lon,
            'cnt' => $cnt,
            'lang' => $lang,
            'mode' => $mode,
            'units' => $units,
        ];
        return self::query($params);
    }
}