<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\StoryEditorAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//RevealAsset::register($this);
StoryEditorAsset::register($this);

$this->title = 'Редактор историй' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sidebarMenuItems'] = [
    ['label' => 'История', 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
];
?>

<div class="row">
	<div class="col-xs-3">
		<div id ="preview-container" style="overflow: auto">
		<?php foreach ($story->getSlides() as $slide): ?>
		<?php $slideIndex = $slide->getSlideNumber() - 1; ?>
			<div class="img-thumbnail preview-container-item" style="height: 164px; width: 218px; margin-bottom: 10px" data-slide-index="<?= $slideIndex ?>">
			<?= Html::a("Слайд {$slide->getSlideNumber()}", '#', ['class' => '', 'onclick' => 'StoryEditor.loadSlide(' . $slideIndex . '); return false']) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
	<div class="col-xs-9" style="height: 484px">
		<div class="reveal" id="story-editor">
			<div class="slides"></div>
		</div>
		<div class="row"><div class="col-xs-12">&nbsp;</div></div>
		<div class="row">
			<div class="col-xs-12">
<?php
$form = ActiveForm::begin([
	'action' => ['/editor/set-slide-text'],
]);
echo $form->field($editorModel, 'text')->textArea(['rows' => 6]);
echo $form->field($editorModel, 'story_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();

$js = <<< JS
$('#{$form->getId()}')
  .on('beforeSubmit', StoryEditor.onBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });
JS;

$textFieldId = Html::getInputId($editorModel, 'text');
$this->registerJs($js);
$js = <<< JS
    StoryEditor.initialize({
    	storyID: {$model->id},
    	textFieldID: '$textFieldId'
    });
    var slideIndex = StoryEditor.readUrl();
    slideIndex = slideIndex || 0;
	StoryEditor.loadSlide(slideIndex);
JS;
$this->registerJs($js);
?>
			</div>
		</div>
	</div>
</div>
