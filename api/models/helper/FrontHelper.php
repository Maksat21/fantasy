<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 01.04.2021
 * Time: 09:59
 */

namespace api\models\helper;


use common\models\helpers\ErrorMsgHelper;
use common\models\Replenishment;
use common\models\Session;
use yii\base\Model;
use yii\data\Pagination;
use yii\web\ConflictHttpException;

class FrontHelper
{
    /**
     * @param Model $form
     * @return array
     * @throws ConflictHttpException
     */
    public static function pagination(Model $form)
    {
        $arr = null;
        if ($form->load(\Yii::$app->request->post(), '') && $form->validate()) {
            $query = $form->pagination();

            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $pages->setPage($form->page);
            $pages->setPageSize($form->limit);

            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            $arr = $form::format($models);
            $pageCount = $pages->getPageCount();
            if ($form->page >= $pageCount - 1) {
                $hasNext = false;
            } else {
                $hasNext = true;
            }
            if ($form->limit == 0) {
                $arr = [];
            }

            return ['status' => 200, 'data' => $arr, 'pages' => $form->page, 'hasNext' => $hasNext, 'pagesCount' => $pageCount];
        } else {
            throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
        }
    }

    /**
     * @param Model $form
     * @return array
     * @throws ConflictHttpException
     */
    public static function helper(Model $form)
    {
        if ($form->load(\Yii::$app->request->post(), '')) {
            $model = $form->save();
            if (!$model) {
//                return ['status' => 400, 'message' => ErrorMsgHelper::getErrorMsg($form)];
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($form));
            }

            if (!is_bool($model) && $model->errors) {
//                return ['status' => 400, 'message' => ErrorMsgHelper::getErrorMsg($form)];
                throw new ConflictHttpException(ErrorMsgHelper::getErrorMsg($model));
            }
            return ['status' => 200];
        } else {
//            return ['status' => 400, 'message' => ErrorMsgHelper::getErrorMsg($form)];
            throw new ConflictHttpException("Не переданы данные");
        }
    }

    public static function getSession($userId)
    {
        $session = Session::find()->where(['user_id' => $userId])->andWhere(['session_end' => null])->one();
        if (!$session) {
            throw new ConflictHttpException('SESSION_NOT_FOUND');
        }
        return $session->id;
    }

    public static function getTotalSum(Model $form)
    {
        $arr = null;
            $form->load(\Yii::$app->request->post(), '');
            $query = $form->pagination();

            $models = $query->all();

            $arr = $form::format($models);

            return ['status' => 200, 'data' => $arr];

    }
}