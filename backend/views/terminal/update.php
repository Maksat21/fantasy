<?php

use common\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Terminal */

$this->title = Yii::t('main', 'UPDATE_TERMINAL: {title}', ['name' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'TERMINALS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('main', 'UPDATE');
?>
<div class="terminal-update">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php Panel::end() ?>

</div>
