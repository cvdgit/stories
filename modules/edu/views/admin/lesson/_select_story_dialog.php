<?php
use backend\widgets\SelectStoryWidget;
use modules\edu\forms\admin\SelectStoryForm;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/**
 * @var View $this
 * @var SelectStoryForm $model
 */
?>
<?php $form = ActiveForm::begin(['id' => 'select-story-form']) ?>
    <div class="modal-header">
        <h5 class="modal-title">Добавить историю</h5>
        <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'story_id')->widget(SelectStoryWidget::class, [
            'id' => 'select-story-slides',
            'onChange' => 'onStoryChange',
        ]) ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>
        <button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
    </div>
<?php ActiveForm::end() ?>
<?php
$this->registerJs(<<<JS
function onStoryChange() {

}
(function() {
    var doneCallback = function(response) {
        if (response && response.success) {
            $('#select-story-modal').modal('hide');
            $.pjax.reload({container: '#pjax-stories', async: false});
        }
        else {
            toastr.error(response['error'] || 'Неизвестная ошибка');
        }
    };
    var failCallback = function(response) {
        toastr.error(response.responseJSON.message);
    }
    var form = $('#select-story-form');
    yiiModalFormInit(form, doneCallback, failCallback);
})();
JS
);
