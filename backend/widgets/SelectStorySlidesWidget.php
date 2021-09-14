<?php

namespace backend\widgets;

use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JsExpression;

class SelectStorySlidesWidget extends Widget
{

    public $slidesAction;
    public $onSave;
    public $selectedSlides;
    public $buttonTitle = 'Выбрать слайды';

    public function run()
    {
        if ($this->slidesAction === null) {
            throw new \DomainException('slidesAction is not set');
        }
        $this->registerClientScript();
        return $this->render('story-slides', [
            'buttonTitle' => $this->buttonTitle,
        ]);
    }

    private function registerClientScript(): void
    {
        $view = $this->getView();
        $configJson = Json::htmlEncode([
            'slidesAction' => $this->slidesAction,
            'onSave' => new JsExpression($this->onSave),
            'selectedSlides' => $this->selectedSlides,
        ]);
        $js = new JsExpression("window.selectStorySlidesConfig = $configJson;");
        $view->registerJs($js);
    }
}
