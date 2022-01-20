<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Attraction */

$this->title = Yii::t('app', 'CREATE ATTRACTION');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ATTRACTIONS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
