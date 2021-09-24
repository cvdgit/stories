<?php
use backend\assets\TestAsset;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StoryTest */
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Изменить тест';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $model->source]],
    $this->title,
];
TestAsset::register($this);
?>
<div class="story-test-update">
    <?php $runTestLink = Html::a('<i class="glyphicon glyphicon-expand"></i>', Yii::$app->urlManagerFrontend->createAbsoluteUrl(['test/view', 'id' => $model->id]), ['id' => 'run-test', 'title' => 'Запустить тест']) ?>
    <?php $runTestLink .= ' ' . Html::a('<i class="glyphicon glyphicon-user"></i>', ['user/user-list'], ['data-toggle' => 'modal', 'data-target' => '#select-user-modal', 'title' => 'Запустить тест от пользователя']) ?>
    <h1><?= Html::encode($this->title) . ($model->isRemote() ? '' : ' ' . $runTestLink) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
        <div class="col-md-6 test-sidebar">
            <?php if (!$model->isNewRecord && !$model->isVariant() && !$model->isTemplate()): ?>
                <?php if ($model->isRemote()): ?>
                    <?= $this->render('_test_children_list', ['model' => $model]) ?>
                <?php endif ?>
                <?php if ($model->isSourceTest()): ?>
                    <?= $this->render('_test_question_list', ['model' => $model, 'dataProvider' => $dataProvider]) ?>
                <?php endif ?>
                <?php if ($model->isSourceWordList()): ?>
                    <?= $this->renderFile('@backend/views/word-list/_list.php', ['model' => new \backend\forms\UpdateWordList($model->wordList)]) ?>
                <?php endif ?>
                <?php if ($model->isSourceTests()): ?>
                    <?= $this->render('_test_tests_list', ['testModel' => $model]) ?>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="modal remote fade modal-fullscreen" id="run-test-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="select-user-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$css = <<< CSS
.run-test {
    padding: 0;
    text-align: center;
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
.modal-fullscreen .modal-dialog {
  width: 80%;
  height: 50%;
  margin-top: 10px;
  padding: 0;
}
.modal-fullscreen .modal-content {
  height: auto;
  min-height: 100%;
}
.modal-fullscreen .story-container {
    background-color: #fff;
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

function initQuestions(params) {
    params = params || {};
    return $.getJSON("/question/init", params);
}

$('#run-test-modal').on('loaded.bs.modal', function() {
    var elem = $("div.new-questions", this),
        params = elem.data();
    var test = WikidsStoryTest.create(elem[0], {
        'dataUrl': '/question/get',
        'dataParams': params,
        'forSlide': false
    });
    initQuestions(params).done(function(response) {
        test.init(response);
    });
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