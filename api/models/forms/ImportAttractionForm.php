<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 01.04.2021
 * Time: 16:56
 */

namespace api\models\forms;


use common\models\Attraction;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportAttractionForm extends Model
{
    const FILENAME = 'xlsImport';

    /**
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file'],
            [['file'], 'required'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        file_put_contents('uploads/xls/' . self::FILENAME . '.xlsx', $this->file);
        $inputFile = 'uploads/xls/' . self::FILENAME . '.xlsx';
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        } catch (\Exception $e) {
            die($e);
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            if ($row == 1) {
                continue;
            }

            $attraction = new Attraction();
            $attraction->name = $rowData[0][0];
            $attraction->price = $rowData[0][1];
            $attraction->balance = 0;
            $attraction->status = Attraction::STATUS_ACTIVE;
            $attraction->save();
        }
        return true;

    }
}