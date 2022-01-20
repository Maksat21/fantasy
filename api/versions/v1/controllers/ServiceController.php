<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 19.02.21
 * Time: 21:08
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use common\models\Setting;
use yii\db\Exception;

class ServiceController extends BaseApiController
{
    public function actionGetWeather()
    {
        $setting = Setting::find()->where(['key' => 'main'])->orWhere(['key' => 'temp'])->orWhere(['key' => 'icon'])->all();
        $main = null;
        $temp = null;
        $icon = null;
        foreach ($setting as $set) {
            if ($set->key == 'main') {
                $main = $set;
            } elseif ($set->key == 'temp') {
                $temp = $set;
            } elseif ($set->key == 'icon') {
                $icon = $set;
            }
        }
        if ($main && $temp && $icon) {
            return ['status' => 200, 'data' => ['name' => $main, 'temp' => $temp, 'icon' => $icon]];
        } else {
            throw new Exception(\Yii::t('api', 'Create the settings {keys} in the admin panel', ['keys' => 'main, temp, icon']));
        }
//        return WeatherHelper::byCoordinates( 42.3140649, 69.5886981,null, 'json', 'metric');
    }

    /**
     * API Курс валют
     * @return array
     * @throws \Exception
     */
    public function actionGetCurrency()
    {
        $setting = Setting::find()
            ->where(['key' => 'usd_sale'])
            ->orWhere(['key' => 'usd_purchase'])
            ->orWhere(['key' => 'eur_sale'])
            ->orWhere(['key' => 'eur_purchase'])
            ->orWhere(['key' => 'rub_sale'])
            ->orWhere(['key' => 'rub_purchase'])
            ->all();
        $usdSale = null;
        $usdPurchase = null;
        $eurSale = null;
        $eurPurchase = null;
        $rubSale = null;
        $rubPurchase = null;
        foreach ($setting as $set) {
            if ($set->key == 'usd_sale') {
                $usdSale = $set;
            } elseif ($set->key == 'usd_purchase') {
                $usdPurchase = $set;
            } elseif ($set->key == 'eur_sale') {
                $eurSale = $set;
            } elseif ($set->key == 'eur_purchase') {
                $eurPurchase = $set;
            } elseif ($set->key == 'rub_sale') {
                $rubSale = $set;
            } elseif ($set->key == 'rub_purchase') {
                $rubPurchase = $set;
            }
        }
        if ($usdSale && $usdPurchase && $eurSale && $eurPurchase && $rubSale && $rubPurchase) {
            $arr[] = (object)['title' => 'USD', 'sale' => $usdSale, 'purchase' => $usdPurchase];
            $arr[] = (object)['title' => 'EUR', 'sale' => $eurSale, 'purchase' => $eurPurchase];
            $arr[] = (object)['title' => 'RUB', 'sale' => $rubSale, 'purchase' => $rubPurchase];
            $result['currency'] = $arr;
            return $result;
        } else {
            throw new Exception(\Yii::t('api', 'Create the setting {keys} in the admin panel', ['keys' => 'usd_sale, usd_purchase, eur_sale, eur_purchase, rub_sale, rub_purchase']));
        }
    }

    /**
     * Текст на главную
     * @return array
     * @throws \Exception
     */
    public function actionGetHead()
    {
        $setting = Setting::find()
            ->where(['key' => 'head_title'])
            ->orWhere(['key' => 'head_text_1'])
            ->all();
        $title = null;
        $text1 = null;
        foreach ($setting as $set) {
            if ($set->key == 'head_title') {
                $title = $set->value;
            } elseif ($set->key == 'head_text_1') {
                $text1 = $set->value;
            }
        }
        $result['title'] = $title;
        $result['text1'] = $text1;

        return $result;
    }

    /**
     * Текст о нас
     * @return array
     * @throws \Exception
     */
    public function actionGetAbout()
    {
        $setting = Setting::find()
            ->orWhere(['key' => 'head_text_2'])
            ->orWhere(['key' => 'head_text_3'])
            ->all();
        $text2 = null;
        $text3 = null;
        foreach ($setting as $set) {
            if ($set->key == 'head_text_2') {
                $text2 = $set->value;
            } elseif ($set->key == 'head_text_3') {
                $text3 = $set->value;
            }
        }
        $result['text2'] = $text2;
        $result['text3'] = $text3;

        return $result;
    }
}