<?php
use common\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTest */
/** @var $form yii\widgets\ActiveForm */
/** @var $dataProvider yii\data\ActiveDataProvider */
$css = <<< CSS
.remote-questions-block {

}
CSS;
$this->registerCss($css);
?>
<div class="story-test-form">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description_text')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'remote')->checkbox() ?>
            <div class="remote-questions-block" style="display: <?= $model->isRemote() ? 'block' : 'none' ?>">
                <?= $form->field($model, 'question_list')->dropDownList([], ['prompt' => 'Загрузка...', 'disabled' => true]) ?>
                <?= $form->field($model, 'question_list_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'question_list_name')->hiddenInput()->label(false) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-6">
            <?php if (!$model->isNewRecord && !$model->isRemote()): ?>
                <div>
                    <p>
                        <?= Html::a('Новый вопрос', ['test/create-question', 'test_id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    </p>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['class' => 'table-responsive'],
                        'columns' => [
                            'name',
                            [
                                'class' => ActionColumn::class,
                                'template' => '{update} {delete}',
                                'urlCreator' => function($action, $model, $key, $index) {
                                    $url = '';
                                    if ($action === 'update') {
                                        $url = Url::to(['test/update-question', 'question_id' => $model->id]);
                                    }
                                    if ($action === 'delete') {
                                        $url = Url::to(['test/delete-question', 'question_id' => $model->id]);
                                    }
                                    return $url;
                                },
                            ],
                        ],
                    ]) ?>
                </div>
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
