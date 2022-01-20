<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 23.08.2018
 * Time: 16:28
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use common\models\UserBreaking;

/**
 * Sync on login
 * Class SyncController
 * @package api\versions\v1\controllers
 */
class SyncController extends BaseApiController
{
    /**
     * @return array
     */
    public function actionHistory(){
        return ['status' => 200, 'adsList' => []];
    }

    /**
     * @return array
     */
    public function actionFavorites(){
        return ['status' => 200, 'list' => []];
    }

    /**
     * @return array
     */
    public function actionBreaking(){
        $where = 'user_breaking.user_id ='.$this->userId. ' and user_breaking.deadline_at > '.time() . ' and user_breaking.status IN(2,3)';
        $query = UserBreaking::find()
            ->where($where);
        return ['status' => 200, 'adsList' => $query->all()];
    }
}