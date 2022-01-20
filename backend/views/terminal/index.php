<?php

use common\widgets\Panel;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TerminalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('main', 'TERMINALS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="terminal-index">

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
            'title',
            'price',
            'login',
//            'access_token:ntext',
//            'password_hash',
            //'f_token',
            //'os_type',
            'app_ver',
            'status',
            'created_at',
            //'updated_at',

            ['class' => '\common\components\grid\ActionColumn',
                'template' => '{update}{delete}'],
        ],
    ]); ?>

    <?php Panel::end() ?>

</div>
