<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 04.05.2021
 * Time: 16:09
 */

namespace api\models\forms;


use common\models\Session;
use yii\base\Model;

class SessionListForm extends Model
{
    public $page;
    public $limit;
    public $cashierId;
    public $dateFrom;
    public $dateTo;

    public static function format($models)
    {
        $list = [];

        foreach ($models AS $session) {
            $list[] = (object)[
                'id' => $session['id'],
                'fullname' => preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.', $session['fullname']),
                'start' => $session['session_start'],
                'end' => $session['session_end'],
//                'action' => "Изменить",
            ];
        }

        return $list;
    }

    public function rules()
    {
        return [
            [['cashierId', 'page', 'limit'], 'integer'],
            [['dateFrom', 'dateTo'], 'safe'],
            [['page', 'limit', 'cashierId'], 'required'],
        ];
    }

    public function pagination()
    {
        $sessions = Session::find()
            ->leftJoin('user', 'user.id=session.user_id')
            ->select(['session.id', 'user.fullname', 'session.session_start', 'session.session_end'])
            ->where(['user_id' => $this->cashierId]);


        if ($this->dateFrom != null) {
            list($day, $month, $year) = explode(".", $this->dateFrom);
            $ymd = "$year-$month-$day";
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $sessions->andWhere([">=", "session.session_start", $ymd . ' 03:00:00']);
            } else {
                $sessions->andWhere([">=", "session.session_start", date("Y-m-d 03:00:00", strtotime('-1 day', strtotime($ymd)))]);
            }
        }

        if ($this->dateTo != null) {
            list($day, $month, $year) = explode(".", $this->dateTo);
            $ymd = "$year-$month-$day";
            if (date("Y-m-d H:i:s") >= date("Y-m-d 03:00:00")) {
                $sessions->andWhere(["<=", "session.session_start", date("Y-m-d 02:59:59", strtotime('+1 day', strtotime($ymd)))]);
            } else {
                $sessions->andWhere(["<=", "session.session_start", $ymd . ' 02:59:59']);
            }
        }

        $query = $sessions
            ->orderBy(['session_start' => SORT_DESC])
            ->asArray();

        return $query;
    }
}