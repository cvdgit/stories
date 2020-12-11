<?php
use common\models\test\SourceType;
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
    <?= $form->field($model, 'source')->dropDownList(SourceType::asArray()) ?>

    <div class="remote-questions-block" style="display: <?= $model->isRemote() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'question_list')->dropDownList([], ['prompt' => 'Загрузка...', 'disabled' => true]) ?>
        <?= $form->field($model, 'question_list_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'question_list_name')->hiddenInput()->label(false) ?>
    </div>

    <div class="word-list-block" style="display: <?= $model->isSourceWordList() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'word_list_id')->dropDownList(\common\models\TestWordList::getWordListAsArray(), ['prompt' => 'Выберите список слов']) ?>
        <?= $form->field($model, 'shuffle_word_list')->checkbox() ?>
    </div>

    <?= $form->field($model, 'answer_type')->dropDownList(\common\models\test\AnswerType::asArray()) ?>

    <div class="answer-block" data-block-type="<?= \common\models\test\AnswerType::INPUT ?>" style="display: <?= $model->isAnswerTypeInput() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'strict_answer')->checkbox() ?>
        <?= $form->field($model, 'input_voice')->dropDownList(\backend\models\test\InputVoice::asArray()) ?>
    </div>

    <div class="answer-block" data-block-type="<?= \common\models\test\AnswerType::RECORDING ?>" style="display: <?= $model->isAnswerTypeRecording() ? 'block' : 'none' ?>">
        <?= $form->field($model, 'input_voice')->dropDownList(\backend\models\test\RecorderLang::asArray()) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord ? 'Создать' : 'Изменить') . ' тест', ['class' => 'btn btn-success']) ?>
        <?= Html::a('История прохождения', ['/history/list', 'test_id' => $model->id], ['class' => 'btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
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
    var block = $('div[data-block-type=' + this.value + ']');
    $('.answer-block').hide();
    if (block.length) {
        block.show();
    }
});
JS;
$this->registerJs($js);