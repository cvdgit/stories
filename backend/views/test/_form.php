<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTest */
/** @var $form yii\widgets\ActiveForm */
/** @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="story-test-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description_text')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'incorrect_answer_text')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'source')->dropDownList(\common\models\StoryTest::testSourcesAsArray()) ?>

    <div class="remote-questions-block" style="display: <?= $model->isRemote() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'question_list')->dropDownList([], ['prompt' => 'Загрузка...', 'disabled' => true]) ?>
        <?= $form->field($model, 'question_list_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'question_list_name')->hiddenInput()->label(false) ?>
    </div>

    <div class="word-list-block" style="display: <?= $model->isSourceWordList() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'word_list_id')->dropDownList(\common\models\TestWordList::getWordListAsArray(), ['prompt' => 'Выберите список слов']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord ? 'Создать' : 'Изменить') . ' тест', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$selected = strtolower(var_export($model->question_list_id, true));
$isSourceNeo = var_export($model->isRemote(), true);
$sourceTest = \common\models\StoryTest::TEST;
$sourceNeo = \common\models\StoryTest::NEO;
$sourceList = \common\models\StoryTest::LIST;
$js = <<< JS
var loaded = false;
var selected = parseInt($selected);
function loadRemoteQuestions() {
    if (loaded) {
        return;
    }
    Neo.getQuestionList().done(function(response) {
        var select = $('#storytest-question_list');
        select
            .empty()
            .append($('<option/>').val('').text('Выберите вопрос'))
            .removeAttr('disabled');
        response.forEach(function(row) {
            var item = $('<option/>')
                .text(row.name)
                .val(row.id);
            if (selected) {
                item.attr('selected', parseInt(row.id) === selected);
            }
            item.appendTo(select);
        });
        loaded = true;
    });
}

var source = $('#storytest-source');
var SOURCE_NEO = $isSourceNeo;

if (SOURCE_NEO) {
    loadRemoteQuestions();
}

source.on('change', function() {
    $('.remote-questions-block').hide();
    $('.word-list-block').hide();
    switch ($(this).val()) {
        case '$sourceTest':
            break;
        case '$sourceNeo':
            $('.remote-questions-block').show();
            loadRemoteQuestions();
            break;
        case '$sourceList':
            $('.word-list-block').show();
            break;
    }
});

$('#storytest-question_list').on('change', function() {
    var id = $(this).val();
    var name = $(this).find('option:selected').text();
    if (id === '') {
        name = '';
    }
    $('#storytest-question_list_id').val(id);
    $('#storytest-question_list_name').val(name);
});
JS;
$this->registerJs($js);