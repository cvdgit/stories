<?php

use frontend\assets\TestAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $students array */
/** @var $activeStudent common\models\UserStudent */
/** @var $category common\models\Category */
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
TestAsset::register($this);
?>
<div class="container">
    <div class="row">
        <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <h3>Ученики</h3>
            <div class="list-group">
                <?php foreach ($students as $student): ?>
                <?php $active = $student['id'] === $activeStudent->id ? ' active' : '' ?>
                <?= Html::a($student['name'], ['test/index', 'category_id' => $category->id, 'student_id' => $student['id']], ['class' => 'list-group-item' . $active]) ?>
                <?php endforeach ?>
            </div>
        </nav>
        <main class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top: 0">
            <h1 style="margin-top: 0; margin-bottom: 20px"><?= Html::a($category->name, $category->getCategoryUrl()) ?> / <?= $this->getHeader() ?></h1>
            <div id="test-list">
                <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemView' => '_item',
                    'viewParams' => [
                        'student' => $activeStudent,
                        'category' => $category,
                    ],
                ]) ?>
            </div>
        </main>
    </div>
</div>

<div class="modal remote fade modal-fullscreen" id="run-test-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal remote fade" id="test-detail-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$css = <<< CSS
#test-list p a {
    text-decoration: underline;
}
#test-list .progress {
    cursor: pointer;
}
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
CSS;
$this->registerCss($css);
$js = <<< JS
$('#test-list').on('click', '.run-test', function(e) {
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

$('#test-detail-modal').on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
});
$('#test-list').on('click', '.progress', function() {
    var bar = $(this).find('.progress-bar');
    var testId = bar.data('testId');
    var studentId = bar.data('studentId');
    $('#test-detail-modal')
        .modal({'remote': '/test/detail?test_id=' + testId + '&student_id=' + studentId});
});
JS;
$this->registerJs($js);