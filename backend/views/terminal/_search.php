<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\TerminalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="terminal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'login') ?>

    <?= $form->field($model, 'access_token') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?= $form->field($model, 'attraction_id') ?>

    <?php // echo $form->field($model, 'f_token') ?>

    <?php // echo $form->field($model, 'os_type') ?>

    <?php // echo $form->field($model, 'app_ver') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
