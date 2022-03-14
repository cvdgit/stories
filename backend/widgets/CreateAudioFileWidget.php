<?php

namespace backend\widgets;

use backend\assets\RecorderAsset;
use yii\base\Widget;
use yii\web\JsExpression;

class CreateAudioFileWidget extends Widget
{

    public $questionId;
    public $audioFileUrl;
    public $callback;

    public function init()
    {
        $view = $this->getView();
        RecorderAsset::register($view);
        parent::init();
    }

    public function run()
    {
        return $this->render('_create_audio_file', [
            'questionId' => $this->questionId,
            'existsAudioUrl' => $this->audioFileUrl,
            'callback' => new JsExpression($this->callback),
        ]);
    }
}