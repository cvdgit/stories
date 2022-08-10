<?php

declare(strict_types=1);

namespace modules\edu\widgets;

use yii\base\Widget;

class AdminToolbarWidget extends Widget
{

    public function run()
    {
        return $this->render('admin-toolbar');
    }
}
