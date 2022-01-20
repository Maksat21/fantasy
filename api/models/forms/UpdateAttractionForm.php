<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 16:56
 */

namespace api\models\forms;


use common\models\Attraction;
use yii\base\Model;

class UpdateAttractionForm extends Model
{
    public $id;
    public $name;
    public $price;

    public function rules()
    {
        return [
            [['price'], 'number'],
            [['id', 'name', 'price'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $attraction = Attraction::findOne(['id' => $this->id]);
        if (!$attraction) {
            return false;
        }
        $attraction->name = $this->name;
        $attraction->price = $this->price;
        if ($attraction->save()) {
            return $attraction;
        } else {
            return false;
        }
    }
}