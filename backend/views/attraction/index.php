<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\widgets\Panel;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AttractionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'ATTRACTIONS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-index">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{create}'
    ]) ?>
    <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>

    <?php Panel::end() ?>

</div>
