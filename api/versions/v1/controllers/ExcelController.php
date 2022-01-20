<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 16:59
 */

namespace api\versions\v1\controllers;

use api\models\AttractionExcelModel;
use api\models\forms\ReplenishmentExcelForm;
use api\models\forms\TransactionPaginationForm;
use api\models\ReplenishmentExcelModel;
use api\models\TransactionExcelModel;
use yii\web\Controller;

class ExcelController extends Controller
{
    public function actionReplenishment($userId = null, $paymentMethod = null, $dateFrom = null, $dateTo = null)
    {
        $rep = new ReplenishmentExcelModel();
        $query = new ReplenishmentExcelForm();
        $query->userId = $userId;
        $query->paymentMethod = $paymentMethod;
        $query->dateFrom = $dateFrom;
        $query->dateTo = $dateTo;
        $rep->getIt($query->filter());
        exit();
    }

    public function actionTransaction($cashier = null)
    {
        $rep = new TransactionExcelModel();
        $query = new TransactionPaginationForm();
        $query->cashier = $cashier;
        $q = $query->pagination();
        $rep->getIt($q->all());
        exit();
    }

    public function actionAttraction()
    {
        $rep = new AttractionExcelModel();
        $rep->getIt();
        exit();
    }
}