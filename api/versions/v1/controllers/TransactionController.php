<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 02.03.2021
 * Time: 14:36
 */

namespace api\versions\v1\controllers;

use api\controllers\BaseApiController;
use common\models\Transaction;

class TransactionController extends BaseApiController
{
    //Вывод истории транзакций
    public function actionHistory()
    {
        $transactions = Transaction::find()->where(['status' => Transaction::STATUS_SUCCESS])->select(['id', 'amount', 'created_at'])->all();
        return ['status' => 200, 'transactions' => $transactions];
    }
}