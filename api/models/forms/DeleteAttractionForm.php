<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 26.03.2021
 * Time: 09:57
 */

namespace api\models\forms;


use common\models\Attraction;
use common\models\Terminal;
use yii\base\Model;

class DeleteAttractionForm extends Model
{
    public $id;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['id'], 'required'],
        ];
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $attraction = Attraction::findOne(['id' => $this->id]);
        if (!$attraction) {
            return false;
        }
        Terminal::deleteAll(['attraction_id' => $this->id]);
        if ($attraction && $attraction->delete()) {
            return true;
        } else {
            return false;
        }
    }
}