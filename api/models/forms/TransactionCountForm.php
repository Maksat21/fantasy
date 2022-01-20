<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 13:47
 */

namespace api\models\forms;


use common\models\Card;
use yii\base\Model;

class TransactionCountForm extends Model
{
    public $attraction;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['attraction'], 'required'],
        ];
    }

}