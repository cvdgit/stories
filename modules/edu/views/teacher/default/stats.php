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
                        <a href="<?= Url::to(['/edu/teacher/default/student-stats', 'id' => $student->id, 'class_program_id' => $classProgram->id, 'class_book_id' => $classBook->id]) ?>" class="student-link"><?= $student->name ?></a>
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
