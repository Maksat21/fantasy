<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 11.03.2021
 * Time: 12:38
 */

namespace console\controllers;

use common\models\helpers\QrHelper;
use common\models\QrCode;
use yii\console\Controller;
use yii\helpers\Console;

class GenerateController extends Controller
{
    /**
     * Создать uuid с qr кодами
     * @param int $count
     * @param string $prefix
     * @return int
     */
    public function actionUuid($count = 100, $prefix = "01")
    {
        Console::startProgress(0, $count);
        $n = round($count / 10);
        $qr = QrCode::find()->orderBy(['id' => SORT_DESC])->one();
        if ($qr) {
            $id = $qr->id;
        } else {
            $id = 0;
        }
        for ($i = 1; $i < $count + 1; $i++) {
            $uuid = $prefix . sprintf("%'.06d", $i + $id);

            $model = new QrCode();
            $model->uuid = $uuid;
//            $model->path = "/123/";
            $model->path = QrHelper::createQr($uuid);
            $model->save();

            if ($n && ($i) % $n == 0) {
                Console::updateProgress($i, $count);
            }
        }

        Console::updateProgress($i - 1, $count);
        Console::endProgress();

        $this->stdout('Done!', Console::FG_GREEN, Console::BOLD);
        return 0;
    }
}