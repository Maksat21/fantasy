<?php

namespace common\components\grid;

use Yii;
use yii\bootstrap\Html;


class ActionColumn extends \yii\grid\ActionColumn
{

    public $contentOptions = [
        'class' => 'action-column',
    ];

    public $template = '{update}{delete}';

    public $options = [
        'width' => 1
    ];

    public $buttonOptions = [
        'class' => 'btn btn-sm btn-default'
    ];

}