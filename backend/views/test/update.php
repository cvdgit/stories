<?php

declare(strict_types=1);

use backend\assets\SortableJsAsset;
use backend\forms\WordListForm;
use backend\models\test\ChangeRepeatForm;
use common\models\StoryTest;
use yii\bootstrap\Dropdown;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $model
 * @var DataProviderInterface $dataProvider
 * @var ChangeRepeatForm $repeatChangeModel
 * @var array $scheduleItems
 * @var array $routes
 */
$this->title = 'Изменить тест';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $model->source]],
    $this->title,
];
SortableJsAsset::register($this);

$this->registerJs($this->renderFile('@backend/views/test/_repetition.js'));
$this->registerJs($this->renderFile('@backend/views/test/_questions-import.js'));
?>
<div class="story-test-update">
    <?php if ($model->isRemote() || $model->isTemplate()): ?>
        <h1><?= Html::encode($this->title) ?></h1>
    <?php else: ?>
        <h1>
            <?= Html::encode($this->title) ?>
            <div class="dropdown pull-right">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default">
                    <i class="glyphicon glyphicon-option-vertical"></i> <b class="caret"></b>
                </a>
                <?= Dropdown::widget([
                    'items' => [
                        ['label' => 'Запустить тест', 'url' => ['test/run', 'id' => $model->id], 'linkOptions' => ['id' => 'run-test']],
                        ['label' => 'Запустить тест от пользователя', 'url' => ['user/user-list'], 'linkOptions' => ['data-toggle' => 'modal', 'data-target' => '#select-user-modal']],
                        ['label' => 'Печать', 'url' => ['question/print', 'test_id' => $model->id], 'linkOptions' => ['data-toggle' => 'modal', 'data-target' => '#print-questions-modal']],
                        ['label' => 'История прохождения', 'url' => ['/history/list', 'test_id' => $model->id], 'visible' => !$model->isTemplate()],
                        [
                            'label' => 'Создать повторение',
                            'url' => ['/repetition/testing/create', 'test_id' => $model->id],
                            'visible' => !$model->isTemplate(),
                            'linkOptions' => ['id' => 'create-repetition'],
                        ],
                        [
                            'label' => 'Список повторений',
                            'url' => ['/repetition/testing/list', 'test_id' => $model->id],
                            'visible' => !$model->isTemplate(),
                            'linkOptions' => ['id' => 'list-repetition'],
                        ],
                        [
                            'label' => 'Импортировать вопросы',
                            'url' => ['/questions-import/form', 'test_id' => $model->id],
                            'visible' => !$model->isTemplate(),
                            'linkOptions' => ['id' => 'questions-import'],
                        ],
                    ],
                ]) ?>
            </div>
        </h1>
    <?php endif ?>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
                'repeatChangeModel' => $repeatChangeModel,
                'scheduleItems' => $scheduleItems,
            ]); ?>
        </div>
        <div class="col-md-6 test-sidebar">
            <?php if (!$model->isNewRecord && !$model->isVariant() && !$model->isTemplate()): ?>
                <?php if ($model->isRemote()): ?>
                    <?= $this->render('_test_children_list', ['model' => $model]) ?>
                <?php endif ?>
                <?php if ($model->isSourceTest()): ?>
                    <?= $this->render('_test_question_list', ['routes' => $routes, 'model' => $model, 'dataProvider' => $dataProvider]) ?>
                <?php endif ?>
                <?php if ($model->isSourceWordList()): ?>
                    <?= $this->renderFile('@backend/views/word-list/_list.php', [
                        'model' => new WordListForm($model->wordList),
                        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->wordList->getTestWords()]),
                    ]) ?>
                <?php endif ?>
                <?php if ($model->isSourceTests()): ?>
                    <?= $this->render('_test_tests_list', ['testModel' => $model]) ?>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="modal remote fade modal-fullscreen" id="run-test-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="select-user-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="print-questions-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$css = <<< CSS
.run-test {
    padding: 0;
    text-align: center;
    height: 100%;
}
.run-test .wikids-test-answer label {
    font-size: 18px;
}
.run-test .wikids-test-questions .question-title {
    font-size: 24px;
}
.run-test .wikids-test-answer img {
    height: 90px;
}

@media (min-width: 768px) {
  .modal-xl {
    width: 90%;
    max-width: 1200px;
  }
}

/*.modal-fullscreen .modal-dialog {
  width: 80%;
  height: 50%;
  margin-top: 10px;
  padding: 0;
}
.modal-fullscreen .modal-content {
  height: auto;
  min-height: 100%;
}*/
.modal-fullscreen .story-container {
    background-color: #fff;
    padding: 0;
}
CSS;
$this->registerCss($css);

$link = Yii::$app->urlManagerFrontend->createAbsoluteUrl(['test/view-by-user', 'id' => $model->id]);
$js = <<< JS
$('#run-test').on('click', function(e) {
    e.preventDefault();
    $('#run-test-modal')
        .modal({'remote': $(this).attr('href')});
});

$('#run-test-modal').on('loaded.bs.modal', function() {
    var elem = $("div.new-questions", this),
        params = elem.data();
    var test = WikidsStoryTest.create(elem[0], {
        'dataUrl': '/question/get',
        'dataParams': params,
        'forSlide': false,
        init: function() {
            return $.getJSON('/question/init', params);
        }
    });
    test.run();
});
$('#run-test-modal').on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
});

$('#select-user-modal').on('loaded.bs.modal', function() {

    $('#select-user-form', this).on('beforeSubmit', function(e) {
        e.preventDefault();

        $('#select-user-modal').modal('hide');

        var userId = $(this).find('#selectuserform-user_id').val();
        var link = '$link' + '&user_id=' + userId;
        $('#run-test-modal')
            .modal({'remote': link});

        return false;
    })
        .on('submit', function(e) {
            e.preventDefault();
        });
});
JS;
$this->registerJs($js);
