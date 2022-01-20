<?php

use common\models\User;
use yii\helpers\Url;
?>
<header class="main-header">
    <a href="<?= Url::to(['/'])?>" class="logo hidden-xs">
        <span class="logo-mini"><i class="fa fa-tachometer" aria-hidden="true"></i></span>
        <span class="logo-lg">Администрация</span>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle visible-xs" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="user user-menu">
                    <a href="<?= Url::to(['site/logout'])?>" data-method="post" class="dropdown-toggle" data-toggle="dropdown" title="Выйти">
                        (<?= User::findOne(['id' => Yii::$app->user->id])->username ?>) <i class="fa fa-power-off"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>