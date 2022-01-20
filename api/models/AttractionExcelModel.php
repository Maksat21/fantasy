<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 31.03.2021
 * Time: 17:58
 */

namespace api\models;


use common\models\Attraction;

class AttractionExcelModel
{
    public function getIt()
    {
        $objPHPExcel = new \PHPExcel();
        $result = Attraction::find()
            ->orderBy(['id' => SORT_DESC])
            ->select(['name', 'price'])
            ->asArray()
            ->all();

        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Название аттракциона');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Стоимость услуги');


        $objPHPExcel->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold(true);

        $rowCount = 2;
        foreach ($result as $row) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, mb_strtoupper($row['name'], 'UTF-8'));
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, mb_strtoupper(round($row['price']) . ' тг', 'UTF-8'));

            $rowCount++;
        }

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="Attraction.xlsx"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}