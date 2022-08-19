<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var UserStudent $student
 * @var EduClassProgram|null $classProgram
 * @var EduClassProgram[] $classPrograms
 */

use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Menu;

$this->title = 'Статистика';

$this->registerCss(<<<CSS
.class-program-list li.active a {
    text-decoration: none;
    color: #99cd50;
}
CSS
);
?>
<div class="container">

    <h1 class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/parent/default/index']) ?> <?= Html::encode($student->name) ?></h1>

    <div style="margin: 30px 0;text-align: center">
        <?= Menu::widget([
            'options' => ['class' => 'class-program-list list-inline'],
            'items' => array_map(static function(EduClassProgram $program) use ($student, $classProgram) {
                return [
                    'label' => $program->program->name,
                    'url' => ['/edu/parent/default/stats', 'id' => $student->id, 'class_program_id' => $program->id],
                    'active' => $classProgram && $classProgram->id === $program->id,
                ];
            }, $classPrograms)
        ]) ?>
    </div>

    <?php if ($classProgram): ?>
    <div>

        <h2 class="h3"><?= $classProgram->program->name ?></h2>

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
    <?php endif ?>
</div>
