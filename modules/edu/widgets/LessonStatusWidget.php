<?php

declare(strict_types=1);

namespace modules\edu\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class LessonStatusWidget extends Widget
{
    public $total;
    public $inProgress;
    public $finished;
    public $tooltip;

    public function run(): string
    {
        $content = '';

        if ($this->total === $this->finished) {
            $content = '<span class="is-done"></span>';
        }

        if ($this->total > 0 && $this->finished === 0) {
            $content = '<span class="not-started" ></span >';
        }

        if ($this->inProgress > 0 || ($this->finished > 0 && $this->finished < $this->total)) {
            $content = '<span class="in-progress"></span>';
        }

        $options = ['class' => 'content-lesson'];
        if ($this->tooltip !== null) {
            $options['data-toggle'] = 'tooltip';
            $options['title'] = $this->tooltip;
        }
        return Html::tag('div', $content, $options);
    }
}
