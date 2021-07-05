<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $story common\models\Story */
/** @var $source backend\models\SourcePowerPointForm */
$form = ActiveForm::begin([
	'action' => ['/story/import-from-power-point'],
]);
echo $form->field($source, 'storyFile')->textInput(['readonly' => true]);
echo $form->field($source, 'slidesNumber')->textInput(['readonly' => true]);
echo $form->field($source, 'storyId')->hiddenInput()->label(false);
?>
<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton('Получить данные', ['class' => 'btn btn-primary', 'disabled' => empty($source->storyFile), 'data-loading-text' => 'Импорт данных из файла PowerPoint...']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
var doneCallback = function(data) {
    if (data) {
        if (data.success) {
            toastr.success("Файл успешно импортирован", "Импорт из PowerPoint");
        }
        else {
            toastr.warning("Произошла ошибка при импорте файла", "Импорт из PowerPoint");
        }
    }
    else {
        toastr.warning("Неизвестная ошибка", "Импорт из PowerPoint");
    }
};
var failCallback = function(response) {
    toastr.error("Произошла ошибка при импорте файла:<br/>" + response.responseJSON.message, "Импорт из PowerPoint");
}
$('#{$form->getId()}')
    .on('beforeSubmit', function(e) {
        e.preventDefault();
      	var form = $(this),
        button = $("button[type=submit]", form);
        button.button("loading");
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: new FormData(this),
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: doneCallback,
            error: failCallback
        }).always(function() {
            button.button("reset");
        });
        return false;
    })
  .on('submit', function(e) {
    e.preventDefault();
  });
JS;
$this->registerJs($js);
