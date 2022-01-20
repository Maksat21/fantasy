<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 18.03.2021
 * Time: 12:26
 */

namespace api\models\forms;


use common\models\Terminal;
use yii\base\Model;

class TerminalForm extends Model
{
    public $name;
    public $login;
    public $password;
    public $attId;
    public $role;

    public function rules()
    {
        return [
            [['name', 'login', 'password', 'role'], 'required'],
            [['name', 'login', 'password'], 'string', 'max' => 255],
            [['attId', 'role'], 'integer'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $terminal = new Terminal();
        $terminal->title = $this->name;
        $terminal->login = $this->login;
        $terminal->setPassword($this->password);
        $terminal->attraction_id = $this->attId;
        $terminal->role = $this->role;
        $terminal->status = Terminal::STATUS_ACTIVE;
        if ($terminal->save()) {
            return $terminal;
        } else {
            return false;
        }
    }
}