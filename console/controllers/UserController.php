<?php

namespace console\controllers;

use yii\base\Model;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;
use common\models\User;

class UserController extends Controller
{
    public function actionIndex()
    {
        echo 'yii user/create' . PHP_EOL;
        echo 'yii user/remove' . PHP_EOL;
        echo 'yii user/activate' . PHP_EOL;
        echo 'yii user/change-password' . PHP_EOL;
    }

    public function actionCreate()
    {
        $model = new User();
//        $this->readValue($model, 'email');
        $this->readValue($model, 'username');
        $model->setPassword($this->prompt('Password:', [
            'required' => true,
            'pattern' => '#^.{8,255}$#i',
            'error' => 'More than 8 symbols',
        ]));
        $model->generateAuthKey();
        $this->log($model->save());
    }

    public function actionRemove()
    {
        $email = $this->prompt('Email:', ['required' => true]);
        $model = $this->findModel($email);
        $this->log($model->delete());
    }

    public function actionActivate()
    {
        $email = $this->prompt('Email:', ['required' => true]);
        $model = $this->findModel($email);
        $model->status = User::STATUS_ACTIVE;
        $model->removeEmailConfirmToken();
        $this->log($model->save());
    }

    /**
     * @throws Exception
     */
    public function actionChangePassword()
    {
        $username = $this->prompt('username:', ['required' => true]);
        $model = $this->findModel($username);
        $model->setPassword($this->prompt('New password:', [
            'required' => true,
            'pattern' => '#^.{6,255}$#i',
            'error' => 'More than 6 symbols',
        ]));
        $this->log($model->save());
    }

    /**
     * @param string $email
     * @throws \yii\console\Exception
     * @return User the loaded model
     */
    private function findModel($username)
    {
        if (!$model = User::findOne(['username' => $username])) {
            throw new Exception('User not found');
        }
        return $model;
    }

    /**
     * @param Model $model
     * @param string $attribute
     */
    private function readValue($model, $attribute)
    {
        $model->$attribute = $this->prompt(mb_convert_case($attribute, MB_CASE_TITLE, 'utf-8') . ':', [
            'validator' => function ($input, &$error) use ($model, $attribute) {
                $model->$attribute = $input;
                if ($model->validate([$attribute])) {
                    return true;
                } else {
                    $error = implode(',', $model->getErrors($attribute));
                    return false;
                }
            },
        ]);
    }

    /**
     * @param bool $success
     */
    private function log($success)
    {
        if ($success) {
            $this->stdout('Success!', Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr('Error!', Console::FG_RED, Console::BOLD);
        }
        echo PHP_EOL;
    }
}