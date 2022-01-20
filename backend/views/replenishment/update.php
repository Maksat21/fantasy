<?php

use common\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Replenishment */

$this->title = 'Update Replenishment: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'REPLENISHMENTS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('main', 'REPLENISHMENTS');
?>
<div class="replenishment-update">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php Panel::end() ?>

</div>
