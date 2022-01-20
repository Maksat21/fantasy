<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 26.03.2021
 * Time: 09:58
 */

namespace api\models\forms;


use common\models\User;
use yii\base\Model;

class DeleteUserForm extends Model
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
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function save()
    {
        if (!$this->validate()) {
            return $this->errors;
        }
        $user = User::findOne(['id' => $this->id]);
        if ($user) {
            if ($user->delete()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}