<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 06.04.2018
 * Time: 22:37
 */

namespace api\models\forms;


use common\models\MProfile;
use yii\base\Model;

class CategoriesForm extends Model
{
    public $userId;
    public $cat1;
    public $cat2;
    public $cat3;
    public $cat4;
    public $cat5;
    public $cat6;
    public $cat7;
    public $cat8;
    public $cat9;
    public $cat10;
    public $cat11;
    public $cat12;

    public function rules()
    {
        return [
            [['cat1', 'cat2', 'cat3', 'cat4', 'cat5', 'cat6', 'cat7', 'cat8', 'cat9', 'cat10', 'cat11', 'cat12'], 'required']
        ];
    }

    public function save(){
        $profile = MProfile::findOne(['user_id' => $this->userId]);
        if($profile){
            $profile->cat_1 = $this->cat1;
            $profile->cat_2 = $this->cat2;
            $profile->cat_3 = $this->cat3;
            $profile->cat_4 = $this->cat4;
            $profile->cat_5 = $this->cat5;
            $profile->cat_6 = $this->cat6;
            $profile->cat_7 = $this->cat7;
            $profile->cat_8 = $this->cat8;
            $profile->cat_9 = $this->cat9;
            $profile->cat_10 = $this->cat10;
            $profile->cat_11 = $this->cat11;
            $profile->cat_12 = $this->cat12;
            if($profile->save()){
                return true;
            }
        }
        return false;
    }

}