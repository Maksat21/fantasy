<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 26.03.2021
 * Time: 09:58
 */

namespace api\models\forms;


use common\models\Terminal;
use yii\base\Model;

class DeleteTerminalForm extends Model
{
    public $id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['id'], 'required'],
        ];
    }

    /**
     * @return bool|Terminal|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $terminal = Terminal::findOne(['id' => $this->id]);
        if ($terminal) {
            $terminal->delete();
        }
        return $terminal;
    }
}