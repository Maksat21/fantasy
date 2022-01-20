<?php

use common\models\Terminal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Attraction;

/* @var $this yii\web\View */
/* @var $model common\models\Terminal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="terminal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'new_password')->textInput(['value'=>'']) ?>

    <?= $form->field($model, 'status')->dropDownList(Terminal::getStatusList()) ?>

    <?= $form->field($model, 'attraction_id')
        ->dropDownList(ArrayHelper::map(Attraction::find()
            ->asArray()
            ->all(),
            'id', 'name'),
            ['prompt' => 'Выберите аттракцион...', 'class' => 'form-control'])
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('main', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
