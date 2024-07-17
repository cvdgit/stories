<?php

declare(strict_types=1);

namespace backend\components\story;

use yii\helpers\Html;

class SlideContent
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $view;
    /**
     * @var string
     */
    private $audioFileSrc;

    public function __construct(int $id, string $view = '', string $audioFileSrc = '') {
        $this->id = $id;
        $this->view = $view;
        $this->audioFileSrc = $audioFileSrc;
    }

    public function __toString()
    {
        return Html::tag('section', '', [
            'data-id' => $this->id,
            'data-slide-view' => $this->view,
            'data-audio-src' => $this->audioFileSrc,
        ]);
    }
}
