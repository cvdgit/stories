<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var EduClassBook $classBook
 * @var EduClassProgram $classProgram
 */

use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Статистика';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1 class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/default/index']) ?> <?= Html::encode($classProgram->program->name . ' / ' . $classBook->name) ?></h1>

    <div>

        <?php if (count($classBook->students) === 0): ?>

        <div>

            <p class="lead">Нет данных.</p>

        </div>

        <?php else: ?>

        <?php foreach ($classBook->students as $student): ?>

        <div>

            <h2 class="h3"><?= $student->name ?></h2>

            <div>
                <?php foreach ($classProgram->eduTopics as $topic): ?>
                <div>

                    <div>
                        <?php foreach ($topic->eduLessons as $lesson): ?>
                        <div>

                            <h3 class="h4"><?= $topic->name . ' / ' . $lesson->name ?></h3>

                            <table class="table table-hover table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>История</th>
                                        <th>Прогресс</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($lesson->stories as $story): ?>
                                    <tr>
                                        <td><?= $story->title ?></td>
                                        <td><?= ($progress = $story->findStudentStoryProgress($student->id)) !== null ? $progress->progress : 'Нет' ?></td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>

        <?php endforeach ?>

        <?php endif ?>
    </div>
</div>
