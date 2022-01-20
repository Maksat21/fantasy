<?php

use common\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Terminal */

$this->title = Yii::t('main', 'CREATE_TERMINAL');
$this->params['breadcrumbs'][] = ['label' => Yii::t('main', 'TERMINALS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="terminal-create">

    <?php Panel::begin([
        'title' => $this->title,
        'buttonsTemplate' => '{cancel}'
    ])?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php Panel::end() ?>

</div>
