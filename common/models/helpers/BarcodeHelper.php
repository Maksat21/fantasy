<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 11.03.2021
 * Time: 18:03
 */

namespace common\models\helpers;


class BarcodeHelper
{
    /**
     * @param $text
     * @return string
     */
    public static function createBarcode($text)
    {
        $ext = self::getExtension($text);
        $dir = md5(time() . uniqid()) . "." . $ext;
        $prePath = substr($dir, 0, 5);
        $tempPath = '';
        for ($i = 0; $i < 5; $i++) {
            $tempPath .= '/' . $prePath[$i];
        }
        $tempPath .= '/';
        $path = \Yii::getAlias('@static') . "/web/barcode";
        if (!file_exists($path . $tempPath)) {
            mkdir($path . $tempPath, 0775, true);
        }
        self::barcode($tempPath, $text);
        $text .= '.png';
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

    /**
     * @param $path
     * @param string $text
     * @param string $code_type
     * @param bool $print
     * @return string
     */
    public static function barcode($path, $text, $print = true)
    {
        $filepath = \Yii::getAlias('@static') . "/web/barcode" . $path . $text . ".png";
        $code_string = "";
        $chksum = 104;
        $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
        $code_keys = array_keys($code_array);
        $code_values = array_flip($code_keys);
        for ($X = 1; $X <= strlen($text); $X++) {
            $activeKey = substr($text, ($X - 1), 1);
            $code_string .= $code_array[$activeKey];
            $chksum = ($chksum + ($code_values[$activeKey] * $X));
        }
        $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];
        $code_string = "211214" . $code_string . "2331112";
        $code_length = 20;
        if ($print) {
            $text_height = 30;
        } else {
            $text_height = 0;
        }

        for ($i = 1; $i <= strlen($code_string); $i++) {
            $code_length = $code_length + (integer)(substr($code_string, ($i - 1), 1));
        }

        $img_width = $code_length;
        $img_height = "20";

        $image = imagecreate($img_width, $img_height + $text_height);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $white);
        if ($print) {
            imagestring($image, 5, 31, $img_height, $text, $black);
        }

        $location = 10;
        for ($position = 1; $position <= strlen($code_string); $position++) {
            $cur_size = $location + (substr($code_string, ($position - 1), 1));
            imagefilledrectangle($image, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black));
            $location = $cur_size;
        }

        imagepng($image, $filepath);
        imagedestroy($image);
        return $filepath;
    }
}