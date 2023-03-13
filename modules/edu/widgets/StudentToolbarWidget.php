<?php

declare(strict_types=1);

namespace modules\edu\widgets;

use yii\base\Widget;

class StudentToolbarWidget extends Widget
{
    public $studentName;
    public $studentClassName;

    public function run()
    {
        return $this->render('student-toolbar', [
            'studentName' => $this->studentName,
            'studentClassName' => $this->studentClassName,
        ]);
    }
}
