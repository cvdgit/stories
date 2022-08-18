<?php

declare(strict_types=1);


namespace modules\edu\widgets;

use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use yii\base\Widget;

class StudentToolbarWidget extends Widget
{

    /**
     * @var UserStudent
     */
    public $student;

    public function run()
    {
        return $this->render('student-toolbar', [
            'student' => $this->student,
        ]);
    }
}
