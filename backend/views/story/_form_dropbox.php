<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'action' => ['/story/import-from-dropbox'],
]);
echo $form->field($source, 'storyFile')->textInput(['readonly' => true]);
echo $form->field($source, 'storyId')->hiddenInput()->label(false);
echo Html::submitButton('Получить данные из Dropbox', ['class' => 'btn btn-primary']);
ActiveForm::end();

$js = <<< JS
$('#{$form->getId()}')
  .on('beforeSubmit', storyOnBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });
JS;
$this->registerJs($js);
