<?php

declare(strict_types=1);

use common\models\Story;
use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\query\StudentQuestionFetcher;
use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Menu;

/**
 * @var View $this
 * @var UserStudent $student
 * @var EduClassProgram|null $classProgram
 * @var EduClassProgram[] $classPrograms
 * @var array $stat
 * @var Story[] $storyModels
 * @var StudentQuestionFetcher $questionFetcher
 * @var EduClassBook $classBook
 */

$this->title = 'Статистика';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1 class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/default/class-program-stats', 'class_book_id' => $classBook->id, 'class_program_id' => $classProgram->id]) ?> <?= Html::encode($student->name) ?></h1>

    <div style="margin: 30px 0;text-align: center">
        <?= Menu::widget([
            'options' => ['class' => 'class-program-list list-inline'],
            'items' => array_map(static function(EduClassProgram $program) use ($student, $classProgram, $classBook) {
                return [
                    'label' => $program->program->name,
                    'url' => ['/edu/teacher/default/student-stats', 'class_book_id' => $classBook->id, 'id' => $student->id, 'class_program_id' => $program->id],
                    'active' => $classProgram && $classProgram->id === $program->id,
                ];
            }, $classPrograms)
        ]) ?>
    </div>

    <?php if ($classProgram): ?>
        <?= $this->render('@modules/edu/views/common/_student_stat', [
            'classProgram' => $classProgram,
            'student' => $student,
            'stat' => $stat,
            'storyModels' => $storyModels,
            'questionFetcher' => $questionFetcher,
        ]); ?>
    <?php endif ?>
</div>
