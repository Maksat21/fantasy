<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 11.03.2021
 * Time: 17:13
 */

namespace common\models\helpers;

use Da\QrCode\QrCode;

class QrHelper
{
    /**
     * Создание qr и сохранение в web/static
     * @param $text
     * @return string
     */
    public static function createQr($text)
    {
        $qrCode = (new QrCode($text))
            ->setSize(250)
            ->setMargin(5)
            ->useForegroundColor(30, 48, 86);
        $text .= '.png';
//        $ext = self::getExtension($text);
//        $dir = md5(time() . uniqid()) . "." . $ext;
        $path = \Yii::getAlias('@static') . '/web/qr/';
//        $prePath = substr($dir, 0, 5);
        $tempPath = '';
//        for ($i = 0; $i < 5; $i++) {
//            $tempPath .= '/' . $prePath[$i];
//        }
//        $tempPath .= '/';
//        if (!file_exists($path . $tempPath)) {
//            mkdir($path . $tempPath, 0775, true);
//        }
        $qrCode->writeFile($path . $tempPath . $text);
        return $tempPath . $text;
    }

    /**
     * @param $fileName
     * @return bool|string
     */
    private static function getExtension($fileName)
    {
        $pos = strrpos($fileName, ".");
        if ($pos !== false) {
            return substr($fileName, $pos);
        } else {
            return "";
        }
    }
}