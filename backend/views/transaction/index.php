<?php

use common\models\Transaction;
use common\widgets\Panel;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('main', 'TRANSACTIONS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => ''
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'terminal_id',
                'format' => 'html',
                'value' => function (Transaction $model) {
                    $terminal = $model->getTerminal()->one();
                    return Html::tag('span', $terminal->title);
                },
            ],
            [
                'attribute' => 'card_id',
                'format' => 'html',
                'value' => function (Transaction $model) {
                    $card = $model->getCard()->one();
                    return Html::tag('span', $card->code);
                },
            ],
            'amount',
            'created_at',
            //'updated_at',

            ['class' => '\common\components\grid\ActionColumn',
                'template' => ''],
        ],
    ]); ?>

    <?php Panel::end() ?>

</div>
