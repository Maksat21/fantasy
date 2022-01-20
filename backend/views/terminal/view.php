<?php

use common\widgets\Panel;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Terminal */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'TERMINALS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="terminal-view">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'login',
            'access_token:ntext',
            'password_hash',
            'f_token',
            'os_type',
            'app_ver',
            'attraction_id',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <?php Panel::end() ?>

</div>
