<?php
use backend\models\test\InputVoice;
use backend\models\test\RecorderLang;
use backend\models\test\TestRepeat;
use backend\widgets\CreateTestTemplateWidget;
use common\models\test\AnswerType;
use common\models\test\SourceType;
use common\models\test\TestTemplateParts;
use common\models\User;
use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\TestTypeOptions;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTest */
/** @var backend\models\test\ChangeRepeatForm $repeatChangeModel */
$opt = new TestTypeOptions($model->answer_type);
?>
<div class="story-test-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?php
    $headerField = $form->field($model, 'header')->textInput(['maxlength' => true]);
    if ($model->isTemplate()) {
        $headerField->hint('Подстановки: ' . TestTemplateParts::asText());
    }
    echo $headerField;
    $descriptionField = $form->field($model, 'description_text')->widget(Widget::class, [
        'settings' => [
            'lang' => 'ru',
            'minHeight' => 200,
            'buttons' => ['html', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'alignment', 'horizontalrule'],
            'plugins' => [
                'fontcolor',
                'fontsize',
            ],
        ],
    ]);
    if ($model->isTemplate()) {
        $descriptionField->hint('Подстановки: ' . TestTemplateParts::asText());
    }
    echo $descriptionField;
    ?>
    <?= $form->field($model, 'show_descr_in_questions')->checkbox() ?>
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

    <?= $form->field($model, 'strict_answer', $opt->forGroup([AnswerType::INPUT]))
        ->checkbox($opt->forField([AnswerType::INPUT])) ?>

    <?= $form->field($model, 'ask_question', $opt->forGroup([AnswerType::DEFAULT, AnswerType::INPUT, AnswerType::RECORDING]))
        ->checkbox(array_merge($opt->forField([AnswerType::DEFAULT, AnswerType::INPUT, AnswerType::RECORDING]), ['data-radio' => 'input', 'data-bound' => Html::getInputId($model, 'ask_question_lang')])) ?>

    <?= $form->field($model, 'ask_question_lang', $opt->forGroup([AnswerType::DEFAULT, AnswerType::INPUT, AnswerType::RECORDING]))
        ->dropDownList(InputVoice::asArray(), $opt->forField([AnswerType::DEFAULT, AnswerType::INPUT, AnswerType::RECORDING])) ?>

    <?= $form->field($model, 'say_correct_answer', $opt->forGroup([AnswerType::INPUT, AnswerType::RECORDING]))
        ->checkbox(array_merge($opt->forField([AnswerType::INPUT, AnswerType::RECORDING]), ['data-radio' => 'input', 'data-bound' => Html::getInputId($model, 'input_voice')])) ?>

    <?= $form->field($model, 'input_voice', $opt->forGroup([AnswerType::INPUT, AnswerType::RECORDING]))
        ->dropDownList(InputVoice::asArray(), $opt->forField([AnswerType::INPUT, AnswerType::RECORDING])) ?>

    <?= $form->field($model, 'voice_response', $opt->forGroup([AnswerType::DEFAULT, AnswerType::RECORDING]))
        ->checkbox(array_merge($opt->forField([AnswerType::DEFAULT, AnswerType::RECORDING]), ['data-bound' => Html::getInputId($model, 'recording_lang')])) ?>

    <?= $form->field($model, 'recording_lang', $opt->forGroup([AnswerType::DEFAULT, AnswerType::RECORDING]))
        ->dropDownList(RecorderLang::asArray(), $opt->forField([AnswerType::DEFAULT, AnswerType::RECORDING])) ?>

    <?= $form->field($model, 'remember_answers', $opt->forGroup([AnswerType::RECORDING]))
        ->checkbox($opt->forField([AnswerType::RECORDING])) ?>

    <?= $form->field($model, 'hide_question_name', ['options' => ['class' => 'form-group']])->checkbox() ?>
    <?= $form->field($model, 'hide_answers_name')->checkbox() ?>
    <?= $form->field($model, 'answers_hints')->checkbox() ?>

    <?php if ($model->isNewRecord): ?>
    <?= $form->field($model, 'repeat')->dropDownList(TestRepeat::getForDropdown()) ?>
    <?php else: ?>
    <div style="display: flex; align-items: center">
        <div style="flex: 1; margin-right: 20px">
            <?= $form->field($model, 'repeat')->dropDownList(TestRepeat::getForDropdown(), ['disabled' => true]) ?>
        </div>
        <div>
            <button class="btn" type="button" data-toggle="modal" data-target="#change-repeat-modal">Изменить</button>
        </div>
    </div>
    <?php endif ?>

    <?= $form->field($model, 'sortable')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <div style="display: flex; flex-direction: row">
            <div style="margin-right: auto">
                <?= Html::submitButton(($model->isNewRecord ? 'Создать' : 'Изменить') . ' тест', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<div>
    <?php if (!$model->isNewRecord && !$model->isTemplate() && ($model->isSourceWordList() || $model->isSourceTest())): ?>
        <?= CreateTestTemplateWidget::widget(['testId' => $model->id]) ?>
    <?php endif ?>
</div>

<?php if ($repeatChangeModel !== null): ?>
<?= $this->render('_repeat_modal', [
    'model' => $model,
    'repeatChangeModel' => $repeatChangeModel,
    'inputId' => Html::getInputId($model, 'repeat'),
]) ?>
<?php endif ?>

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

(function() {

    $('#storytest-answer_type').on('change', function() {
        var currentType = this.value;
        $('[data-types]').each(function(i, elem) {
            $(elem).addClass('hide');
            $(elem).find('input,select').prop('disabled', true);
            $(elem).find('input:checkbox').prop('checked', false);
            var types = $(elem).attr('data-types').split(',');
            if (types.includes(currentType)) {
                $(elem).removeClass('hide');
                $(elem).find('input,select').removeAttr('disabled');
            }
        });
        setControlsState();
    });

    function setControlsState() {
        $('[data-bound]').each(function(i, elem) {
            var id = $(elem).attr('data-bound');
            var targetElem = $('#' + id);
            targetElem.attr('disabled', !elem.checked);
            if (elem.checked) {
                targetElem.parents('.form-group:eq(0)').removeClass('hide');
            }
        });
    }

    setControlsState();

    $('[data-bound]').on('click', function() {
        var id = $(this).attr('data-bound');
        $('#' + id).attr('disabled', !this.checked);
    });

    $('[data-radio]').on('click', function() {
        var group = $(this).attr('data-radio');
        $('[data-radio=' + group + ']').prop('disabled', this.checked);
        $(this).prop('disabled', false);
    });
})();
JS;
$this->registerJs($js);
