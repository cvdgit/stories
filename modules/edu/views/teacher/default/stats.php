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
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Статистика';

$this->registerCss(<<<CSS
.testing-item {
    user-select: none;
    cursor: pointer;
    display: flex;
    flex-direction: row;
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
    <?= TeacherMenuWidget::widget() ?>

    <h1 class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/default/index']) ?> <?= Html::encode($classProgram->program->name . ' / ' . $classBook->name) ?></h1>

    <div id="edu-stats">

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
                                        <th>Тесты</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($lesson->stories as $story): ?>
                                    <tr>
                                        <td><?= $story->title ?></td>
                                        <td><?= ($progress = $story->findStudentStoryProgress($student->id)) !== null ? $progress->progress : 'Нет' ?></td>
                                        <td><a class="show-testing" href="<?= Url::to(['/edu/teacher/default/story-testing', 'story_id' => $story->id, 'student_id' => $student->id]) ?>">Результаты</a></td>
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
})();
JS
);
