<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 07.05.2021
 * Time: 10:45
 */

namespace api\models\forms;


use common\models\Card;
use common\models\Replenishment;
use common\models\Transaction;
use yii\base\Model;
use yii\data\Pagination;
use yii\web\ConflictHttpException;

class OperationListForm extends Model
{
    public $page;
    public $limit;
    public $sessionId;
    public $code;
    public $operationType;

    public function rules()
    {
        return [
            [['page', 'limit', 'sessionId', 'operationType'], 'integer'],
            [['page', 'limit'], 'required'],
            [['code'], 'safe'],
        ];
    }

    public function pagination()
    {
        if ($this->code) {
            $card = Card::findOne(['code' => $this->code]);
            if (!$card) {
                throw new ConflictHttpException('Нет карточки');
            }
        }
        $list = [];
        $this->limit /= 2;

        if ($this->operationType == 0 || $this->operationType == null) {
            $replenishments = Replenishment::find()
                ->leftJoin('user', 'user.id=replenishment.user_id')
                ->select(['replenishment.amount', 'replenishment.created_at', 'user.fullname'])
                ->where(['session_id' => $this->sessionId])
                ->orderBy(['created_at' => SORT_DESC])
                ->asArray();

            if ($this->sessionId != null) {
                $replenishments->andWhere(['session_id' => $this->sessionId]);
            }

            if ($this->code) {
                $replenishments->andWhere(['card_id' => $card->id]);
            }
            $countQuery = clone $replenishments;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $pages->setPage($this->page);
            $pages->setPageSize($this->limit);

            $replenishments = $replenishments->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            foreach ($replenishments AS $replenishment) {
                $list[] = (object)[
                    'amount' => $replenishment['amount'],
                    'type' => 0,
                    'date' => $replenishment['created_at'] ? $replenishment['created_at'] : '',
                    'description' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $replenishment['fullname']),
                ];
            }
        }

        if ($this->operationType == 1 || $this->operationType == null) {
            $transactions = Transaction::find()
                ->leftJoin('terminal', 'terminal.id=transaction.terminal_id')
                ->leftJoin('attraction', 'attraction.id=terminal.attraction_id')
                ->select(['attraction.name', 'amount', 'transaction.created_at'])
                ->where(['session_id' => $this->sessionId])
                ->orderBy(['created_at' => SORT_DESC])
                ->asArray();

            if ($this->sessionId != null) {
                $transactions->andWhere(['session_id' => $this->sessionId]);
            }

            if ($this->code) {
                $transactions->andWhere(['card_id' => $card->id]);
            }
            $countQuery = clone $transactions;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $pages->setPage($this->page);
            $pages->setPageSize($this->limit);

            $transactions = $transactions->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            foreach ($transactions AS $transaction) {
                $list[] = (object)[
                    'amount' => $transaction['amount'],
                    'type' => 1,
                    'date' => $transaction['created_at'] ? $transaction['created_at'] : '',
                    'description' => $transaction['name'],
                ];
            }
        }

        array_multisort(array_column($list, 'date'), SORT_DESC, $list);

        foreach ($list as $l) {
            $l->date = \Yii::$app->formatter->asDatetime($l->date, 'php:H:i d.m.Y');
        }

        return ['list' => $list, 'count' => $pages->getPageCount()];
    }

}