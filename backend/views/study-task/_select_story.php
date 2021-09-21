<?php
use backend\widgets\SelectStoryWidget;
use backend\widgets\SelectStorySlidesWidget;
use yii\helpers\Html;
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\study_task\BaseStudyTaskForm */
/** @var $isTaskStory bool */
$css = <<<CSS
.study-task-form fieldset {
    border: 1px #eee solid !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow: 0px 0px 0px 0px #000;
            box-shadow: 0px 0px 0px 0px #000;
}
.study-task-form fieldset legend {
    font-size: 1.2em !important;
    font-weight: bold !important;
    text-align: left !important;
    width: auto;
    padding: 0 10px;
    border-bottom: none;
}
CSS;
$this->registerCss($css);
?>
<fieldset style="margin-bottom:10px" id="story-slides">
    <legend>История или слайды</legend>
    <?php if (!$isTaskStory): ?>
    <?= $form->field($model, 'story_id')->widget(SelectStoryWidget::class, [
        'loadUrl' => ['story/autocomplete/select-all'],
        'storyModel' => $model->getStory(),
    ]) ?>
    <?php endif ?>
    <?php if ($model->isNewRecord() || $isTaskStory): ?>
    <div>
        <?= SelectStorySlidesWidget::widget([
            'slidesAction' => 'story/widget/slides',
            'onSave' => 'onSaveSlides',
            'buttonTitle' => 'Выбрать слайды',
            'selectedSlides' => $model->getStorySlides(),
        ]) ?>
        <div class="selected-slides" style="margin-top:10px">
            <p>Слайды не выбраны</p>
        </div>
    </div>
    <?php endif ?>
    <?php if ($model->haveStory()): ?>
        <div style="margin-bottom:20px">
            <h4>История - задание создана</h4>
            <?= Html::a('Просмотр задания', $model->getModel()->getStudyTaskUrlBackend(), ['class' => 'btn btn-sm btn-primary', 'target' => '_blank']) ?>
            <?= Html::a('Редактировать историю', ['editor/edit', 'id' => $model->getStoryID()], ['class' => 'btn btn-sm btn-primary', 'target' => '_blank']) ?>
        </div>
    <?php endif ?>
</fieldset>
<?php
$js = <<< JS
function onSaveSlides(selected, modal) {

    var list = $('#story-slides').find('.selected-slides');
    list.empty();
    
    selected = selected || [];
    if (selected.length > 0) {
        $('<p/>', {'text': 'Слайды выбраны. История будет создана после сохранения.'})
            .appendTo(list);
    }

    selected.forEach(function(slideID) {
        list.append($('<input/>', {
            'type': 'hidden',
            'name': 'CreateStudyTaskForm[slide_ids][]',
            'value': slideID
        }));
    });
    modal.modal('hide');
}
JS;
$this->registerJs($js);