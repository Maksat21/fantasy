<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 17:37
 */

namespace api\models;

use common\models\Replenishment;

class ReplenishmentExcelModel
{
    public function getIt($result)
    {
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ID номер карты');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Сумма пополнения');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Дата пополнения');

        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Кассир');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Способ оплаты');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Бонусы');


        $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);

        $rowCount = 2;
        foreach ($result as $row) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, mb_strtoupper(trim(chunk_split($row['code'], 3, ' ')), 'UTF-8'));
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, mb_strtoupper(round($row['amount']) . ' тг', 'UTF-8'));
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, mb_strtoupper(\Yii::$app->formatter->asDatetime($row['created_at'], 'php:H:i d.m.Y'), 'UTF-8'));

            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, mb_strtoupper(preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $row['fullname']), 'UTF-8'));
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, mb_strtoupper(Replenishment::getStaticPaymentLabel($row['payment_method']), 'UTF-8'));
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, mb_strtoupper($row['name'], 'UTF-8'));

            $rowCount++;
        }

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="Replenishment.xlsx"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}