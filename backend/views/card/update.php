<?php

use common\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Card */

$this->title = 'Update Card: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'CARDS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('main', 'UPDATE');
?>
<div class="card-update">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php Panel::end() ?>

</div>
