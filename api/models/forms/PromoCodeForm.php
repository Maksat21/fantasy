<?php
/**
 * Created by PhpStorm.
 * User: maks
 * Date: 10/29/19
 * Time: 16:56
 */

namespace api\models\forms;


use common\models\helpers\ErrorMsgHelper;
use common\models\MUser;
use common\models\VolRef;
use yii\base\Model;

class PromoCodeForm extends Model
{
    public $userId;
    public $promoCode;

    public function rules()
    {
        return ['userId', 'required'];
    }

    public function getPromo(){
        $user = MUser::findOne($this->userId);
        if($user->promo_code){
            $this->promoCode = $user->promo_code;
            return true;
        }
        
        $volRef = VolRef::findOne(['user_id' => $this->userId]);
        if(!$volRef){
            $volRef = new VolRef();
            $volRef->symbols = $volRef->generateRandomSymbols();
            $volRef->user_id = $this->userId;
            if(!$volRef->save()){
                $this->addError('userId', ErrorMsgHelper::getErrorMsg($volRef));
                return false;
            }
        }
        $this->promoCode = $volRef->createRef();
        if($user){
            $user->promo_code = $this->promoCode;
            if($user->save()){
                return true;
            }
        }
        return false;
    }
}