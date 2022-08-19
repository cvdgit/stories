<?php

declare(strict_types=1);

namespace modules\edu\widgets;

use yii\base\Widget;

class AdminHeaderWidget extends Widget
{

    public $title;
    public $content;

    public function run()
    {
        return $this->render('admin-header', [
            'title' => $this->title,
            'content' => $this->content,
        ]);
    }
}
