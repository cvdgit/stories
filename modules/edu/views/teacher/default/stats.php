<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var EduClassBook $classBook
 * @var EduClassProgram $classProgram
 * @var array $lastActivities
 */

use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\widgets\LessonStatusWidget;
use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Статистика';

$this->registerCss(<<<CSS
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
.table-stat {
    position: relative;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    width: 100%;
}
.left-col {
    max-width: 384px;
    width: 100%;
    flex: 1 0 auto;
}
.right-col {
    max-width: calc(100% - 384px);
    width: 100%;
    flex: 1 0 100%;
    display: flex;
    margin-bottom: -17px;
    position: relative;
    overflow-x: auto;
}
.table-head {
    width: 100%;
}
.table-body {
    width: 100%;
}
.table-cell {
    height: 40px;
    padding: 12px 8px;
    display: inline-block;
    vertical-align: top;
    box-sizing: border-box;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    position: relative;
    z-index: 15;
    background: white;
    -webkit-transition: all 0.2s ease-in-out;
    -moz-transition: all 0.2s ease-in-out;
    transition: all 0.2s ease-in-out;
}
.table-header-cell {
    line-height: 14px;
    word-wrap: normal;
    white-space: normal;
    padding: 11px 8px 8px;
    font-weight: bold;
    color: #000;
    text-transform: none;
    text-align: left;
    font-size: 1.4rem;
}
.table-cell.size-1 {
    width: 80px;
}
.table-cell.size-2 {
    width: 160px;
}
.student-link {
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    transition: all ease .3s;
    color: #99CD50;
}
.right-col-inner {
    display: flex;
    padding-bottom: 17px;
    transition: transform .25s ease-in-out;
}
.topic-col {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    width: auto;
    padding-right: 20px;
    align-items: flex-start;
}
.topic-cell {
    width: 100%;
    max-width: 100%;
    flex: 1 0 100%;
    white-space: nowrap;
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
CSS
);
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1 class="h2" style="margin-bottom:40px">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/default/index']) ?> <?= Html::encode($classBook->name . ' / ' . $classProgram->program->name) ?>
    </h1>

    <div style="margin-bottom: 100px">
        <div class="table-stat">
            <div class="left-col">
                <div class="table-head">
                    <div class="table-cell table-header-cell size-2">Ученик</div>
                    <div class="table-cell table-header-cell size-1">Прогресс</div>
                    <div class="table-cell table-header-cell size-1">Посл. активн.</div>
                </div>
                <div class="table-body">
                    <?php foreach ($classBook->students as $student): ?>
                    <div class="table-cell size-2">
                        <a href="<?= Url::to(['/edu/teacher/default/student-stats', 'id' => $student->id, 'class_program_id' => $classProgram->id]) ?>" class="student-link"><?= $student->name ?></a>
                    </div>
                    <div class="table-cell size-1"><?= $classProgram->getStudentProgress($student->id) ?>%</div>
                    <div class="table-cell size-1"><?= $lastActivities[$student->id] ?? '-' ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="right-col">
                <div class="right-col-inner">
                    <?php foreach ($classProgram->eduTopics as $topic): ?>
                    <div class="topic-col">
                        <div class="table-head">
                            <div class="table-cell table-header-cell topic-cell"><?= $topic->name ?></div>
                        </div>
                        <div class="table-body">
                        <?php foreach ($classBook->students as $student): ?>
                            <div class="table-cell topic-cell">
                                <div style="white-space: nowrap;">
                                    <div style="display: inline-block; margin-right: 15px">
                                    <?php foreach ($topic->eduLessons as $lesson): ?>
                                        <?= LessonStatusWidget::widget([
                                            'total' => $lesson->getStoriesCount(),
                                            'finished' => $lesson->getStudentFinishedStoriesCount($student->id),
                                            'inProgress' => $lesson->fetchStudentInProgressStoriesCount($student->id),
                                        ]) ?>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
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
