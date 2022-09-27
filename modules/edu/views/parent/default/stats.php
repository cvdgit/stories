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
.student-progress-cell__header {
    color: #aab8be;
    text-transform: uppercase;
    font-size: 14px;
    font-weight: bold;
    text-align: left;
    padding: 11px 8px 8px;
}
.student-progress-table__body {
    display: flex;
    position: relative;
}
.student-progress-table {
    max-width: min-content;
    width: 100%;
}
.student-progress-table-content {
    max-width: 688px;
    width: 100%;
    overflow: hidden;
    position: relative;
}
.topic-row {
    position: relative;
    width: 100%;
    height: 40px;
    padding-right: 30px;
}
.topic-row__cell {
    padding: 12px 8px;
    display: inline-block;
    vertical-align: top;
    box-sizing: border-box;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.content-row {
    position: relative;
    width: 100%;
    height: 40px;
}
.topic-row:nth-child(even), .content-row:nth-child(even) {
    background-color: #f2f2f3;
}
.content-row__cell {
    padding: 12px 8px;
    display: inline-block;
    vertical-align: top;
    box-sizing: border-box;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.content-lesson {
    display: inline-block;
    margin-right: 5px;
    padding-top: 2px;
    font-size: 13px;
}
.content-lesson span {
    box-sizing: border-box;
    display: inline-block;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    position: relative;
    top: 2px;
    margin: 0 0 0 0;
}
.content-lesson .not-started {
    background-color: #d3d3d3;
}
.content-lesson .in-progress {
    background-color: #6fc4e2;
}
.content-lesson .is-done {
    background-color: #37ae68;
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

    <div style="margin-bottom: 100px">

        <div style="display: flex; border-bottom: 2px solid #aaa">
            <div style="max-width:min-content;width:100%">
                <div class="student-progress-cell__header">Тема</div>
            </div>
            <div>
                <div class="student-progress-cell__header">Прогресс</div>
            </div>
        </div>

        <div class="student-progress-table__body">

            <div class="student-progress-table-topics">
                <?php foreach ($classProgram->eduTopics as $topic): ?>
                <div class="topic-row">
                    <div class="topic-row__cell">
                        <?= $topic->name ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>

            <div class="student-progress-table-content">
                <div style="width:100%">
                    <?php foreach ($classProgram->eduTopics as $topic): ?>
                    <div class="content-row">
                        <div class="content-row__cell">
                            <div style="white-space: nowrap">
                                <?php foreach ($topic->eduLessons as $lesson): ?>
                                <div style="display:inline-block;margin-right:15px">
                                    <?php foreach ($lesson->stories as $story): ?>
                                    <?php $progress = $story->findStudentStoryProgress($student->id); ?>
                                    <?php if ($progress === null): ?>
                                    <div class="content-lesson">
                                        <span class="not-started"></span>
                                    </div>
                                    <?php else: ?>
                                    <?php if ($progress->statusInProgress()): ?>
                                    <div class="content-lesson">
                                        <span class="in-progress"></span>
                                    </div>
                                    <?php endif ?>
                                    <?php if ($progress->statusIsDone()): ?>
                                    <div class="content-lesson">
                                        <span class="is-done"></span>
                                    </div>
                                    <?php endif ?>
                                    <?php endif ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>

    <?php endif ?>
</div>
