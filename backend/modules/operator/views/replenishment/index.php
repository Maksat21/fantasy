<?php

use common\models\Replenishment;
use common\widgets\Panel;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ReplenishmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('main', 'REPLENISHMENTS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="replenishment-index">

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
            [
                'attribute' => 'card_id',
                'format' => 'html',
                'value' => function (Replenishment $model) {
                    $card = $model->getCard()->one();
                    return Html::tag('span', $card->code);
                },
            ],
            'amount',
            [
                'attribute' => 'user_id',
                'format' => 'html',
                'value' => function (Replenishment $model) {
                    $product = $model->getUser()->one();
                    return Html::tag('span', $product->username);
                },
            ],
            'created_at',
            //'updated_at',

            ['class' => '\common\components\grid\ActionColumn',
                'template' => '{view}'],
        ],
    ]); ?>

    <?php Panel::end() ?>

</div>
