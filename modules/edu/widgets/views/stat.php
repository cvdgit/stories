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
 */
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
                                    <div style="display:inline-block;margin-right:15px">
                                        <?php foreach ($topic->eduLessons as $lesson): ?>
                                            <?= LessonStatusWidget::widget([
                                                'total' => $lesson->getStoriesCount(),
                                                'finished' => $lesson->getStudentFinishedStoriesCount($student->id),
                                                'inProgress' => $lesson->fetchStudentInProgressStoriesCount($student->id),
                                                'tooltip' => $lesson->name,
                                                'lessonId' => $lesson->id,
                                            ]) ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>

    <div id="edu-stats">
        <?php foreach ($stat as $item): ?>
            <div>
                <h2 class="h3 text-center" style="margin-bottom: 30px"><?= SmartDate::dateSmart(strtotime($item['date'])) ?></h2>
                <div>
                    <?php foreach ($item['topics'] as $topicItem): ?>
                        <h3 class="h4" style="margin-bottom: 30px"><?= Html::encode($topicItem['topicName']) ?></h3>
                        <div>
                            <?php foreach ($topicItem['lessons'] as $lessonItem): ?>
                                <h4 data-lesson-id="<?= $lessonItem['lessonId']; ?>" class="h5"><?= Html::encode($lessonItem['lessonName']) ?></h4>
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="col-md-4">История</th>
                                                <th class="col-md-2">Прогресс</th>
                                                <th class="col-md-2">Время</th>
                                                <th class="col-md-2">Ответов на вопросы / неправильных</th>
                                                <th class="col-md-2">Тесты</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($lessonItem['stories'] as $storyId): ?>
                                                <?php $story = $storyModels[$storyId]; ?>
                                                <tr>
                                                    <td><?= $story->title ?></td>
                                                    <td><?= ($progress = $story->findStudentStoryProgress($student->id)) !== null ? $progress->progress . '%' : 'Нет' ?></td>
                                                    <td><?= $timeFetcher->fetch($student->id, (int)$storyId, $item['date']); ?></td>
                                                    <td>
                                                        <?php $questionData = $questionFetcher->fetch($student->id, (int)$storyId, $item['date']); ?>
                                                        <?= $questionData['total'] . ($questionData['incorrect'] > 0 ? ' / ' . $questionData['incorrect'] : ''); ?>
                                                    </td>
                                                    <td><a class="show-testing" href="<?= Url::to(['/edu/teacher/default/story-testing', 'story_id' => $story->id, 'student_id' => $student->id]) ?>">Результаты</a></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal remote fade" id="test-detail-modal">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

<?php
$this->registerJs(<<<JS
(function() {
    $('#edu-stats').on('click', '.show-testing', function(e) {
        e.preventDefault();

        const thisRow = $(this).parents('tr:eq(0)');

        let testingRow = thisRow.next();
        if (!testingRow.hasClass('testing-row')) {
            testingRow = $('<tr/>', {class: 'testing-row'}).append(
                $('<td/>', {colspan: thisRow.find('td').length})
            );
            testingRow.insertAfter(thisRow);
        }

        const url = $(this).attr('href');

        testingRow.find('td').empty();
        $.getJSON(url)
            .done(function(response) {
                if (response && response.success) {
                    const testings = response.data || [];
                    if (testings.length === 0) {
                        testingRow.find('td').text('Тестирование не найдено');
                        return;
                    }
                    testings.forEach(testing => {
                        const row = $('<div/>', {class: 'testing-item'})
                            .data('resource', testing.resource)
                            .append(
                                $('<div/>', {class: 'testing-item__name'}).text(testing.name)
                            )
                            .append(
                                $('<div/>', {class: 'testing-item__progress'}).text('Прогресс: ' + testing.progress)
                            );
                        testingRow.find('td').append(row);
                    });
                }
            });
    });

    $('#edu-stats').on('click', '.testing-item', function(e) {
        e.preventDefault();
        const resource = $(this).data('resource');
        $('#test-detail-modal').modal({'remote': resource});
    });

    $('#test-detail-modal').on('hide.bs.modal', function() {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });

    $('.student-progress-table-content').on('click', '.content-lesson [data-to-lesson]', function() {
        const lessonId = $(this).attr('data-to-lesson');
        $('#edu-stats [data-lesson-id=' + lessonId + ']:eq(0)')[0].scrollIntoView({behavior: "smooth", block: "start", inline: "start"});
    });
})();
JS
);
