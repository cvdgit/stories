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
    <div class="col-lg-4 col-md-12">
        <?= Html::submitButton('Получить данные', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-lg-4 col-md-12">
        <?= Html::a('Выгрузить файл', ['/story/download', 'id' => $story->id], ['class' => 'btn']) ?>
    </div>
    <div class="col-lg-4 col-md-12">
        <?= Html::a('Read only история', ['/story/readonly', 'id' => $story->id], ['class' => 'btn']) ?>
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
JS;
$this->registerJs($js);
