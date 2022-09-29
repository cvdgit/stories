<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var UserStudent $student
 * @var EduClassProgram|null $classProgram
 * @var EduClassProgram[] $classPrograms
 * @var array $stat
 */

use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use yii\helpers\Html;
use yii\helpers\Url;
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
    border: 2px #d3d3d3 solid;
    background: transparent;
}
.content-lesson .in-progress {
    border: 2px #6fc4e2 solid;
    background: transparent;
}
.content-lesson .is-done {
    background-color: #37ae68;
}
.testing-item {
    user-select: none;
    cursor: pointer;
    display: flex;
    flex-direction: row;
    margin-bottom: 10px;
    padding: 10px 0;
}
.testing-item__name {
    margin-right: auto;
}
.testing-item__progress {

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
                                <div style="display:inline-block;margin-right:15px">
                                <?php foreach ($topic->eduLessons as $lesson): ?>
                                    <div class="content-lesson">
                                        <?php
                                        $storiesCount = $lesson->getStoriesCount();
                                        $finishedStoriesCount = $lesson->getStudentFinishedStoriesCount($student->id);
                                        ?>
                                        <?php if ($storiesCount === $finishedStoriesCount): ?>
                                        <span class="is-done"></span>
                                        <?php endif ?>
                                        <?php if ($storiesCount > 0 && $finishedStoriesCount === 0): ?>
                                        <span class="not-started"></span>
                                        <?php endif ?>
                                        <?php if ($finishedStoriesCount > 0 && $finishedStoriesCount < $storiesCount): ?>
                                            <span class="in-progress"></span>
                                        <?php endif ?>
                                    </div>
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
            <h2 class="h3"><?= $item['date'] ?></h2>
            <div>
            <?php foreach ($item['topics'] as $topicItem): ?>
                <h3 class="h4"><?= Html::encode($topicItem['topicName']) ?></h3>
                <div>
                    <?php foreach ($topicItem['lessons'] as $lessonItem): ?>
                    <h4 class="h5"><?= Html::encode($lessonItem['lessonName']) ?></h4>
                    <div class="row">
                        <div class="col-md-8">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th class="col-md-8">История</th>
                                <th class="col-md-2">Прогресс</th>
                                <th class="col-md-2">Тесты</th>
                            </tr>
                            </thead>
                            <tbody>
                        <?php foreach ($lessonItem['stories'] as $story): ?>
                            <tr>
                                <td><?= $story->title ?></td>
                                <td><?= ($progress = $story->findStudentStoryProgress($student->id)) !== null ? $progress->progress : 'Нет' ?></td>
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

    <?php endif ?>
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
})();
JS
);
