<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTest */
/** @var $form yii\widgets\ActiveForm */
/** @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="story-test-form">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description_text')->textarea(['rows' => 6]) ?>
            <?php if (!$model->haveQuestions()): ?>
            <?= $form->field($model, 'remote')->checkbox() ?>
            <?php endif ?>
            <div class="remote-questions-block" style="display: <?= $model->isRemote() ? 'block' : 'none' ?>">
                <?= $form->field($model, 'question_list')->dropDownList([], ['prompt' => 'Загрузка...', 'disabled' => true]) ?>
                <?= $form->field($model, 'question_list_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'question_list_name')->hiddenInput()->label(false) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton(($model->isNewRecord ? 'Создать' : 'Изменить') . ' тест', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-6">
            <?php if (!$model->isNewRecord): ?>
                <?php if ($model->isRemote()): ?>
                <?= $this->render('_test_children_list', ['model' => $model]) ?>
                <?php else: ?>
                <?= $this->render('_test_question_list', ['model' => $model, 'dataProvider' => $dataProvider]) ?>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?php
$selected = strtolower(var_export($model->question_list_id, true));
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
$('#storytest-remote').on('click', function() {
    var checked = $(this).prop('checked');
    $('.remote-questions-block').toggle();
    loadRemoteQuestions();
});
if ($('#storytest-remote').prop('checked')) {
    loadRemoteQuestions();
}
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
