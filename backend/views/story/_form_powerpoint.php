<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'action' => ['/story/import-from-power-point'],
]);
echo $form->field($source, 'storyFile')->textInput(['readonly' => true]);
echo $form->field($source, 'slidesNumber')->textInput(['readonly' => true]);
echo $form->field($source, 'storyId')->hiddenInput()->label(false);
echo Html::submitButton('Получить данные из PowerPoint', ['class' => 'btn btn-primary']);
echo Html::a('Выгрузить файл', ['/story/download', 'id' => $story->id], ['class' => 'btn', 'style' => 'margin-left: 20px']);
ActiveForm::end();

$js = <<< JS
$('#{$form->getId()}')
  .on('beforeSubmit', storyOnBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });
JS;
$this->registerJs($js);
