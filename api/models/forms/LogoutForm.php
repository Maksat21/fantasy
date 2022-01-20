<?php
/**
 * Created by PhpStorm.
 * User: фора
 * Date: 27.04.2021
 * Time: 10:28
 */

namespace api\models\forms;


use common\models\User;
use yii\base\Model;
use yii\web\ConflictHttpException;

class LogoutForm extends Model
{
    public $userId;
    public $_user;

    public function rules()
    {
        return [
            [['userId'], 'required'],
        ];
    }

    /**
     * @return User|null
     * @throws ConflictHttpException
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['id' => $this->userId]);
            if (!$this->_user) {
                throw new ConflictHttpException('Нет пользователя');
            }
        }

        return $this->_user;
    }

}