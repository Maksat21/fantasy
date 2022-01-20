<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 26.03.2021
 * Time: 09:58
 */

namespace api\models\forms;


use common\models\Discount;
use yii\base\Model;

class DeleteDiscountForm extends Model
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
     * @return bool|Discount|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $discount = Discount::findOne(['id' => $this->id]);
        if ($discount) {
            $discount->delete();
        }
        return $discount;
    }
}