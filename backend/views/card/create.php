<?php

use common\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Card */

$this->title = Yii::t('main', 'CREATE_CARD');
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'CARDS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-create">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php Panel::end() ?>

</div>
