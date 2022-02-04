<?php

namespace backend\widgets;

use yii\base\Widget;
use yii\web\JsExpression;

class SelectCategoriesWidget extends Widget
{

    public $selectInputID;
    public $treeID;
    public $onSelect = '{}';

    public function run(): string
    {
        return $this->render('select-categories', [
            'selectInputID' => $this->selectInputID,
            'treeID' => $this->treeID,
            'onSelect' => new JsExpression($this->onSelect),
        ]);
    }
}