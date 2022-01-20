<?php

use common\widgets\Panel;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Replenishment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'REPLENISHMENTS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="replenishment-view">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <div id="print-content">
        123
<!--        print_r($model);-->
    </div>

    <a class="btn btn-sm btn-default btn btn-primary" onClick="javascript:printResult();" title="Распечатать">Распечатать</a>

    <?php Panel::end() ?>

</div>
