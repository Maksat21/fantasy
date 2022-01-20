<?php

use common\widgets\Panel;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('main', 'CARDS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-index">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{create}'
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'code',
            'balance',
            'created_at',

            ['class' => '\common\components\grid\ActionColumn',
                'template' => '{update}{delete}'],
        ],
    ]); ?>

    <?php Panel::end() ?>

</div>
