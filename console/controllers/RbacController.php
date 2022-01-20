<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\components\rbac\Role;

class RbacController extends Controller {

    public function actionInit()
    {
        if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
            return self::EXIT_CODE_NORMAL;
        }

        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();

        $admin = $auth->createRole(1);
        $admin->description = 'Admin';
        $auth->add($admin);

        $cashier = $auth->createRole(2);
        $cashier->description = 'Cashier';
        $auth->add($cashier);

        $accountant = $auth->createRole(3);
        $accountant->description = 'Accountant';
        $auth->add($accountant);

        $auth->addChild($admin, $cashier);
        $auth->addChild($admin, $accountant);

        $this->stdout('Done!' . PHP_EOL);
    }
}