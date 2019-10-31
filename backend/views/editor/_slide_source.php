<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'slide-source-form'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Исходный код слайда</h4>
</div>
<div class="modal-body">
    <?php echo $form->field($model, 'source')->textArea(['rows' => 20])->label(false) ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end(); ?>

<?php
$js = <<< JS
function slideSourceOnBeforeSubmit(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: new FormData(form[0]),
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            $('#slide-source-modal').modal('hide');
            StoryEditor.loadSlide(StoryEditor.getCurrentSlideID(), true);
        },
        error: function(data) {
            toastr.warning('', data);
        }
    });
    return false;
}
$('#slide-source-form')
  .on('beforeSubmit', slideSourceOnBeforeSubmit)
  .on('submit', function(e) {
      e.preventDefault();
  });
JS;
$this->registerJs($js, View::POS_READY);
?>
