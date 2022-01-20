<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 26.09.2018
 * Time: 10:38
 */

namespace api\versions\v1\controllers;


use api\controllers\BaseApiController;
use api\models\forms\NotificationForm;
use common\models\Badge;
use common\models\helpers\ErrorMsgHelper;
use common\models\MNotification;
use Yii;
use yii\data\Pagination;
use yii\web\ConflictHttpException;

/**
 * Class NotificationController
 * @package api\versions\v1\controllers
 */
class NotificationController extends BaseApiController
{
    public function actionList(){
        $notificationForm = new NotificationForm();
        $formData    = $this->getDecodedBodyData();
        $requestData = $this->getSpecificData();
        $formData    = array_merge($formData, $requestData);
        $notificationForm->userId = $this->userId;
        $arr = null;

        if($notificationForm->load($formData, '') && $notificationForm->validate()){
            $query = MNotification::find()
                ->select(['title', 'description', 'created_at'])
                ->where(['status' => MNotification::STATUS_ACTIVE])
                ->andWhere('user_id is null or user_id = '.$this->userId);

            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $pages->setPage($notificationForm->page);
            $pages->setPageSize($notificationForm->limit);

            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            foreach ($models as $item) {
                $arr[] = (object) $item->toArray();
            }
            $pageCount = $pages->getPageCount();
            if($notificationForm->page >= $pageCount-1) {
                $hasNext = false;
            } else {$hasNext = true;}
            return ['status' => 200, 'data' => $arr, 'pages' => $notificationForm->page, 'hasNext' => $hasNext];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($notificationForm));
        }
    }
}