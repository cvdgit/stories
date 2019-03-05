<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\RevealAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

RevealAsset::register($this);

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
	<?php foreach ($story->getSlides() as $slide): ?>
		<div class="img-thumbnail" style="height: 164px; width: 218px; margin-bottom: 10px">
		<?php foreach ($slide->getBlocks() as $block): ?>
			<?php if (get_class($block) == 'backend\components\SlideBlockImage'): ?>
			<?= Html::a(Html::img($block->getSrc(), ['height' => 154]), '#', ['onclick' => 'StoryEditor.loadSlide(' . ($slide->getSlideNumber() - 1) . '); return false']) ?>
			<?php endif ?>
		<?php endforeach ?>
		</div>
	<?php endforeach ?>
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
	StoryEditor.loadSlide(0);
JS;
$this->registerJs($js);
?>
			</div>
		</div>
	</div>
</div>
