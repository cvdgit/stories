<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use common\models\Story;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStudent;
use modules\edu\query\StudentQuestionFetcher;
use modules\edu\query\StudentStoryDurationFetcher;
use modules\edu\widgets\LessonStatusWidget;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var EduClassProgram|null $classProgram
 * @var EduStudent $student
 * @var array $stat
 * @var Story[] $storyModels
 * @var StudentQuestionFetcher $questionFetcher
 * @var StudentStoryDurationFetcher $timeFetcher
 * @var bool $canClearHistory
 */

$this->registerJs($this->renderFile('@modules/edu/widgets/views/_stat.js'));
?>
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
            <?php
            foreach ($classProgram->eduTopics as $topic): ?>
                <div class="topic-row">
                    <div class="topic-row__cell">
                        <?= $topic->name ?>
                    </div>
                </div>
            <?php
            endforeach ?>
        </div>

        <div class="student-progress-table-content">
            <div style="width:100%">
                <?php
                foreach ($classProgram->eduTopics as $topic): ?>
                    <div class="content-row">
                        <div class="content-row__cell">
                            <div style="white-space: nowrap">
                                <div style="display:inline-block;margin-right:15px">
                                    <?php
                                    foreach ($topic->eduLessons as $lesson): ?>
                                        <?= LessonStatusWidget::widget([
                                            'total' => $lesson->getStoriesCount(),
                                            'finished' => $lesson->getStudentFinishedStoriesCount($student->id),
                                            'inProgress' => $lesson->fetchStudentInProgressStoriesCount($student->id),
                                            'tooltip' => $lesson->name,
                                            'lessonId' => $lesson->id,
                                        ]) ?>
                                    <?php
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach ?>
            </div>
        </div>
    </div>
</div>

<div id="edu-stats">
    <?php
    foreach ($stat as $item): ?>
        <div>
            <h2 class="h3 text-center" style="margin-bottom: 30px"><?= SmartDate::dateSmart(
                    strtotime($item['date']),
                ) ?></h2>
            <div>
                <?php
                foreach ($item['topics'] as $topicItem): ?>
                    <h3 class="h4" style="margin-bottom: 30px"><?= Html::encode($topicItem['topicName']) ?></h3>
                    <div>
                        <?php
                        foreach ($topicItem['lessons'] as $lessonItem): ?>
                            <h4 data-lesson-id="<?= $lessonItem['lessonId']; ?>" class="h5"><?= Html::encode(
                                    $lessonItem['lessonName'],
                                ) ?></h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="col-md-4">История</th>
                                            <th class="col-md-2">Общий прогресс</th>
                                            <th class="col-md-2">Время сессии</th>
                                            <th class="col-md-2">Содержимое</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($lessonItem['stories'] as $storyId): ?>
                                            <?php
                                            $story = $storyModels[$storyId]; ?>
                                            <tr>
                                                <td><?= $story->title ?></td>
                                                <td><?= ($progress = $story->findStudentStoryProgress(
                                                        $student->id,
                                                    )) !== null ? $progress->progress . '%' : 'Нет' ?></td>
                                                <td><?= $timeFetcher->fetch(
                                                        $student->id,
                                                        (int) $storyId,
                                                        $item['date'],
                                                    ); ?></td>
                                                <td>
                                                    <a class="show-detail btn-outline-info" href="<?= Url::to(
                                                        [
                                                            '/edu/teacher/default/story-detail',
                                                            'story_id' => $story->id,
                                                            'student_id' => $student->id,
                                                            'date' => $item['date'],
                                                        ],
                                                    ) ?>">Подробнее</a>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($canClearHistory): ?>
                                                        <a onclick="return confirm('Подтверждаете?')" href="<?= Url::to(
                                                            [
                                                                '/edu/default/clear-story-history',
                                                                'story_id' => $story->id,
                                                                'student_id' => $student->id,
                                                            ],
                                                        ); ?>" title="Очистить прогресс по этой истории для ученика"><i
                                                                class="glyphicon glyphicon-trash"></i></a>
                                                    <?php
                                                    endif; ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php
                        endforeach ?>
                    </div>
                <?php
                endforeach ?>
            </div>
        </div>
    <?php
    endforeach; ?>
</div>

<div class="modal remote fade" id="test-detail-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="mental-map-detail-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
