<?php

use yii\bootstrap\Dropdown;
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
    <div class="col-lg-4 col-md-12">
        <?= Html::submitButton('Получить данные', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-lg-8 col-md-12">
        <div class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle btn">Действия <b class="caret"></b></a>
            <?php
            echo Dropdown::widget([
                'items' => [
                    ['label' => 'Выгрузить файл', 'url' => ['/story/download', 'id' => $story->id]],
                    ['label' => 'Read only история', 'url' => ['/story/readonly', 'id' => $story->id], 'linkOptions' => ['id' => 'readonly-story']],
                    ['label' => 'Текст истории', 'url' => ['/story/text', 'id' => $story->id]],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
<?php
ActiveForm::end();
$js = <<< JS
$('#{$form->getId()}')
  .on('beforeSubmit', storyOnBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });

$("#readonly-story").on("click", function(e) {
    e.preventDefault();
    $.get($(this).attr("href")).done(function(data) {
        if (data && data.success) {
            toastr.success("Успешно");
        }
        else {
            toastr.error("Ошибка");
        }
    });
});
JS;
$this->registerJs($js);
