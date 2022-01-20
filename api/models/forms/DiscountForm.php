<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 14:24
 */

namespace api\models\forms;


use common\models\Discount;
use yii\base\Model;

class DiscountForm extends Model
{
    public $name;
    public $type;
    public $quantity;

    public function rules()
    {
        return [
            [['quantity', 'type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name', 'type', 'quantity'], 'required'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $discount = new Discount();
        $discount->name = $this->name;
        $discount->type = $this->type;
        $discount->quantity = $this->quantity;
        if ($discount->save()) {
            return $discount;
        } else {
            return false;
        }
    }
}