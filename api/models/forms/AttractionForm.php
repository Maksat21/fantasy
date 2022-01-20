<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 14:24
 */

namespace api\models\forms;


use common\models\Attraction;
use yii\base\Model;

class AttractionForm extends Model
{
    public $name;
    public $price;

    public function rules()
    {
        return [
            [['price'], 'number'],
            [['name', 'price'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $attraction = new Attraction();
        $attraction->name = $this->name;
        $attraction->price = $this->price;
        $attraction->status = Attraction::STATUS_ACTIVE;
        if ($attraction->save()) {
            return $attraction;
        } else {
            return false;
        }
    }
}