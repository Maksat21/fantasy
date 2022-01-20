<?php

namespace common\components\grid;


use Yii;
use yii\bootstrap\Html;

class GridView extends \yii\grid\GridView{

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{summary}':
                return $this->renderSummary();
            case '{items}':
                return $this->renderItems();
            case '{pager}':
                return $this->renderPager();
            case '{sorter}':
                return $this->renderSorter();
            case '{counter}':
                return $this->renderCounter();
            default:
                return false;
        }
    }

    protected function renderCounter()
    {

        $result = '<div class="grid-counter pull-right">Выводить по: <div class="btn-group">';

        foreach (array(20, 50, 100, 200) as $i){

            $params = [
                Yii::$app->controller->getRoute()
            ];

            foreach (Yii::$app->request->getQueryParams() as $key => $value){
                $params[$key] = $value;
            }

            $params['show'] = $i;

            $result .= Html::a($i, Yii::$app->urlManager->createAbsoluteUrl($params), ['class' => 'btn btn-default btn-sm' . ($this->dataProvider->getCount() == $i ? ' btn-primary' : '')]);
        }

        $result .= '</div></div>';

        return $result;
    }

}