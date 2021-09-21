<?php
use backend\widgets\SelectStorySlidesWidget;
use backend\widgets\SelectStoryWidget;
use backend\widgets\StudyTaskAssignWidget;
use common\models\study_task\StudyTaskStatus;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\study_task\BaseStudyTaskForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $assignDataProvider yii\data\ActiveDataProvider */
$css = <<<CSS
.study-task-form fieldset {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}
.study-task-form fieldset legend {
    font-size: 1.2em !important;
    font-weight: bold !important;
    text-align: left !important;
    width:auto;
    padding:0 10px;
    border-bottom:none;
}
CSS;
$this->registerCss($css);
?>
<div class="row">
    <div class="col-md-7">
        <div class="study-task-form">
            <?php $form = ActiveForm::begin(['id' => 'study-task-form']); ?>
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'status')->dropDownList(StudyTaskStatus::asArray(), ['disabled' => true]) ?>
            <fieldset style="margin-bottom:10px">
                <legend>История или слайды</legend>
                <?= $form->field($model, 'story_id')->widget(SelectStoryWidget::class, [
                        'loadUrl' => ['story/autocomplete/select-all'],
                        'storyModel' => $model->getStory(),
                ]) ?>
                <div>
                    <?php if (!$model->haveStory()): ?>
                    <p>или</p>
                    <?= SelectStorySlidesWidget::widget([
                        'slidesAction' => 'story/widget/slides',
                        'onSave' => 'onSaveSlides',
                        'selectedSlides' => $model->getStorySlides(),
                        'buttonTitle' => $model->haveStory() ? 'Изменить список слайдов' : 'Выбрать слайды'
                    ]) ?>
                    <div class="selected-slides">
                        <?php if (!$model->haveStory()): ?>
                            <p>Слайды не выбраны</p>
                        <?php endif ?>
                    </div>
                    <?php endif ?>
                    <?php if ($model->haveStory()): ?>
                        <div style="margin-bottom:20px">
                            <h4>История - задание создана</h4>
                            <?= Html::a('Просмотр задания', $model->getModel()->getStudyTaskUrlBackend(), ['class' => 'btn btn-sm btn-primary', 'target' => '_blank']) ?>
                            <?= Html::a('Редактировать историю', ['editor/edit', 'id' => $model->getStoryID()], ['class' => 'btn btn-sm btn-primary', 'target' => '_blank']) ?>
                        </div>
                    <?php endif ?>
                </div>
            </fieldset>
            <div class="form-group form-group-controls">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="col-md-5">
        <?php if (!$model->isNewRecord()): ?>
        <div style="margin-bottom:10px">
            <?= StudyTaskAssignWidget::widget([
                'studyTaskID' => $model->id,
            ]) ?>
        </div>
        <?= GridView::widget([
                'dataProvider' => $assignDataProvider,
                'columns' => [
                        'name',
                ],
            ]) ?>
        <?php endif ?>
    </div>
</div>

<?php
$className = array_reverse(explode('\\', get_class($model)))[0];
$js = <<< JS
function onSaveSlides(selected, modal) {

    var list = $('#story-slides').find('.selected-slides');
    list.empty();
    
    selected = selected || [];
    if (selected.length > 0) {
        $('<p/>', {'text': 'Слайды выбраны. История будет создана после сохранения.'})
            .appendTo(list);
    }
    
    var modelName = '$className';
    selected.forEach(function(slideID) {
        list.append($('<input/>', {
            'type': 'hidden',
            'name': modelName + '[slide_ids][]',
            'value': slideID
        }));
    });
    modal.modal('hide');
}
JS;
$this->registerJs($js);