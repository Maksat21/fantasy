<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 28.04.2021
 * Time: 13:53
 */

namespace api\versions\v1\controllers;


use api\controllers\FrontApiController;
use api\models\forms\LogoutForm;
use common\models\helpers\ErrorMsgHelper;
use common\models\Replenishment;
use common\models\Session;
use common\models\Transaction;
use yii\web\ConflictHttpException;

class LogoutController extends FrontApiController
{
    /**
     * @return array
     * @throws ConflictHttpException
     */
    public function actionLogout()
    {
        $loginForm = new LogoutForm();
        $loginForm->userId = $this->userId;
        $user = $loginForm->getUser();
//        $incompleteSessions = Session::find()
//            ->where(['user_id' => $user->id])
//            ->andWhere(['session_end' => null])
//            ->all();
//
//        foreach ($incompleteSessions as $session) {
//            $transactions = Replenishment::find()
//                ->select(['sum(amount) as sum', 'count(amount) as count'])
//                ->where(['session_id' => $session->id])
//                ->asArray()
//                ->one();
//
//            $session->transaction_count = $transactions['count'] ? $transactions['count'] : 0;
//            $session->transaction_amount = $transactions['sum'] ? $transactions['sum'] : 0;
//            $session->session_end = date('Y-m-d H:i:s');
//            $session->save();
//        }
        $user->access_token = '';
        if ($user->save()) {
            return ['status' => 200];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($user));
        }
    }
}