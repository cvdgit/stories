<?php
use backend\widgets\CreateTestTemplateWidget;
use common\models\test\AnswerType;
use common\models\test\SourceType;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTest */
/** @var $form yii\widgets\ActiveForm */
?>
<div class="story-test-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description_text')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'created_by')->dropDownList(User::getUserList(),
        ['prompt' => 'Выбрать', 'disabled' => !Yii::$app->user->can('admin')]) ?>
    <?= $form->field($model, 'incorrect_answer_text')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'source')->dropDownList(SourceType::asArray(), ['disabled' => !$model->isNewRecord]) ?>

    <div class="remote-questions-block" style="display: <?= $model->isRemote() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'question_list')->dropDownList([], ['prompt' => 'Загрузка...', 'disabled' => true]) ?>
        <?= $form->field($model, 'question_list_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'question_list_name')->hiddenInput()->label(false) ?>
    </div>

    <div class="word-list-block" style="display: <?= $model->isSourceWordList() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'word_list_id')->dropDownList(\common\models\TestWordList::getWordListAsArray(), ['prompt' => 'Выберите список слов']) ?>
        <?= $form->field($model, 'shuffle_word_list')->checkbox() ?>
    </div>

    <?= $form->field($model, 'answer_type')->dropDownList(AnswerType::asArray()) ?>

    <?php
    $options = ['disabled' => true];
    $askQuestionField = $form->field($model, 'ask_question')->checkbox($options);
    $askQuestionLangField = $form->field($model, 'ask_question_lang')->dropDownList(\backend\models\test\InputVoice::asArray(), $options);
    $strictAnswerField = $form->field($model, 'strict_answer')->checkbox($options);
    $inputVoiceField = $form->field($model, 'input_voice')->dropDownList(\backend\models\test\InputVoice::asArray(), $options);
    $recordingLangField = $form->field($model, 'recording_lang')->dropDownList(\backend\models\test\RecorderLang::asArray(), $options);
    $rememberAnswersField = $form->field($model, 'remember_answers')->checkbox($options);
    $answerBlockOptions = static function(string $type) {
        return ['class' => 'answer-block hide', 'data-block-type' => $type];
    }
    ?>

    <?= Html::tag('div', $askQuestionField . $askQuestionLangField, $answerBlockOptions(AnswerType::DEFAULT)) ?>
    <?= Html::tag('div', $strictAnswerField . $inputVoiceField, $answerBlockOptions(AnswerType::INPUT)) ?>
    <?= Html::tag('div', $recordingLangField . $inputVoiceField . $rememberAnswersField . $askQuestionField . $askQuestionLangField, $answerBlockOptions(AnswerType::RECORDING)) ?>

    <?= $form->field($model, 'hide_question_name')->checkbox() ?>

    <div class="form-group">
        <?= $form->field($model, 'sortable')->hiddenInput()->label(false) ?>
        <?= Html::submitButton(($model->isNewRecord ? 'Создать' : 'Изменить') . ' тест', ['class' => 'btn btn-success']) ?>
        <?= Html::a('История прохождения', ['/history/list', 'test_id' => $model->id], ['class' => 'btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php if (!$model->isNewRecord && !$model->isTemplate() && ($model->isSourceWordList() || $model->isSourceTest())): ?>
    <div class="form-group">
        <?= CreateTestTemplateWidget::widget(['testId' => $model->id]) ?>
    </div>
    <?php endif ?>
</div>

<?php
$selected = strtolower(var_export($model->question_list_id, true));
$isSourceNeo = var_export($model->isRemote(), true);
$sourceTest = SourceType::TEST;
$sourceNeo = SourceType::NEO;
$sourceList = SourceType::LIST;
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

$('#storytest-answer_type').on('change', function() {
    $('.answer-block')
        .addClass('hide')
        .find('input, select')
        .prop('disabled', true);
    var block = $('div[data-block-type=' + this.value + ']');
    if (block.length) {
        block
            .removeClass('hide')
            .find('input, select')
            .prop('disabled', false);
    }
}).change();
JS;
$this->registerJs($js);