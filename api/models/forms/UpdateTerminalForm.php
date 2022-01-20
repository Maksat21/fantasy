<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 16:57
 */

namespace api\models\forms;


use common\models\Terminal;
use yii\base\Model;

class UpdateTerminalForm extends Model
{
    public $id;
    public $name;
    public $login;
    public $password;
    public $attId;
    public $role;

    public function rules()
    {
        return [
            [['id', 'name', 'login', 'role'], 'required'],
            [['name', 'login', 'password'], 'string', 'max' => 255],
            [['attId', 'role'], 'integer'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return ['status' => false, 'code' => 1, 'message' => $this->validate()];
        }
        $terminal = Terminal::findOne(['id' => $this->id]);
        if (!$terminal) {
            return ['status' => false, 'code' => 2, 'message' => 'Нет терминала с таким id'];
//            return false;
        }
        $terminal->title = $this->name;
        $terminal->login = $this->login;
        if ($this->password) {
            $terminal->setPassword($this->password);
        }
        if ($this->attId) {
            $terminal->attraction_id = $this->attId;
        }
        if($this->role == Terminal::ROLE_ADMIN){
            $terminal->attraction_id = null;
        }
        $terminal->role = $this->role;
        $terminal->status = Terminal::STATUS_ACTIVE;
        if ($terminal->save()) {
            return ['status' => true, 'message' => $terminal];
//            return $terminal;
        } else {
            return ['status' => false, 'code' => 3, 'message' => $terminal->getErrorSummary(true)];
//            return false;
        }
    }
}