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

    <h1 class="h2"><?= Html::encode($classProgram->program->name . ' / ' . $classBook->name) ?></h1>

    <div>

        <?php foreach ($classBook->students as $student): ?>

        <div>

            <h2 class="h3"><?= $student->name ?></h2>

            <div>
                <?php foreach ($classProgram->eduTopics as $topic): ?>
                <div>
                    <h3 class="h4"><?= $topic->name ?></h3>

                    <div>
                        <?php foreach ($topic->eduLessons as $lesson): ?>
                        <div>

                            <h4 class="h5"><?= $lesson->name ?></h4>

                            <table class="table table-hover table-sm">
                                <tbody>
                                    <?php foreach($lesson->stories as $story): ?>
                                    <tr>
                                        <td><?= $story->title ?></td>
                                        <td></td>
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

    </div>
</div>
