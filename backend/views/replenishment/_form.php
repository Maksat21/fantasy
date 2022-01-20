<?php

use common\models\Card;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Replenishment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="replenishment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'card_id')->widget(Select2::classname(), [
//                'data' => $model,
        'data' => ArrayHelper::map(Card::find()->all(), 'id', 'code'),
        'value' => 'name',
        'options' => ['placeholder' => Yii::t('main', 'PLEASE_SELECT_PRODUCT')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('main', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
