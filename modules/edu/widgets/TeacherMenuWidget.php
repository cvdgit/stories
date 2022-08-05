<?php

declare(strict_types=1);


namespace modules\edu\widgets;

use yii\base\Widget;

class TeacherMenuWidget extends Widget
{

    public function run()
    {
        return $this->render('teacher-menu');
    }
}
