<?php

use backend\models\SlideEditorForm;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\helpers\Url;

/** @var $model SlideEditorForm */

?>

<div style="margin: 20px 0">
    <?php Modal::begin([
        'header' => '<h2>Ссылка на слайде</h2>',
        'toggleButton' => [
            'label' => 'Ссылка на слайде',
            'class' => 'btn',
        ],
    ]); ?>
    <?php
    $linkForm = ActiveForm::begin(['action' => ['/editor/link'], 'id' => 'link-form']);
    echo $linkForm->field($model->linkForm, 'title')->textInput();
    echo $linkForm->field($model->linkForm, 'url')->textInput();
    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
    ActiveForm::end();
    ?>
    <?php Modal::end(); ?>
</div>

<?php
$js = <<< JS
$('#link-form')
    .on('beforeSubmit', StoryEditor.linkFormSubmit)
    .on('submit', e => e.preventDefault())
JS;
$this->registerJs($js);
?>

<?php
$form = ActiveForm::begin([
    'action' => ['/editor/update-slide'],
    'options' => ['enctype' => 'multipart/form-data'],
]);
echo $form->field($model, 'image')->fileInput();
echo $form->field($model, 'text_size')->textInput();
echo $form->field($model, 'text')->textArea(['rows' => 6]);
echo $form->field($model, 'story_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();

$js = <<< JS
$('#{$form->getId()}')
  .on('beforeSubmit', StoryEditor.onBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });
JS;

$textFieldId = Html::getInputId($model, 'text');
$textSizeFieldId = Html::getInputId($model, 'text_size');
$fileFieldId = Html::getInputId($model, 'image');
$action = Url::to(['/editor/get-slide-by-index', 'story_id' => $model->story_id]);
$this->registerJs($js);
$js = <<< JS
    StoryEditor.initialize({
    	storyID: {$model->story_id},
    	getSlideAction: '$action',
    	textFieldID: '$textFieldId',
    	textSizeFieldID: '$textSizeFieldId',
    	fileFieldID: '$fileFieldId'
    });
    let slideIndex = StoryEditor.readUrl();
    slideIndex = slideIndex || 0;
	StoryEditor.loadSlide(slideIndex);
JS;
$this->registerJs($js);
?>
