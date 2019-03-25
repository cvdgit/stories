<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\assets\StoryEditorAsset;
use common\widgets\RevealWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
$css = <<< CSS
.reveal-container {
	position: relative;
}
.reveal-container::before {
    content: "";
    display: block;
    padding-bottom: calc(100% / (16/9));
    width: 100%;
}
.reveal-container-inner {
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
}
CSS;
$this->registerCss($css);
?>

<div class="row">
	<div class="col-xs-3">
		<div id ="preview-container" style="overflow: auto">
		<?php foreach ($story->getSlides() as $slide): ?>
		<?php $slideIndex = $slide->getSlideNumber() - 1; ?>
			<div class="img-thumbnail preview-container-item" style="height: 164px; width: 218px; margin-bottom: 10px" data-slide-index="<?= $slideIndex ?>">
			<?= Html::a("Слайд {$slideIndex}", '#', ['class' => '', 'onclick' => 'StoryEditor.loadSlide(' . $slideIndex . '); return false']) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
	<div class="col-xs-9">
		<div class="reveal-container">
			<div class="reveal-container-inner">
		    <?= RevealWidget::widget([
		    		'id' => 'story-editor',
		    		'initializeReveal' => false,
		    		'options' => [
		    			'hash' => false,
		    			'history' => false,
		    			'dependencies' => [
			                ["src" => "/js/revealjs-customcontrols/customcontrols.js"],
			                ["src" => "/js/revealjs-customcontrols/customcontrols.css"],
		    			],
		    		],
		    		'controls' => [
		    			new \common\widgets\RevealButtons\FullscreenButton(),
					],
		    	]) ?>
		    </div>
		</div>
		<div class="row"><div class="col-xs-12">&nbsp;</div></div>
		<div class="row">
			<div class="col-xs-12">
<?php
$form = ActiveForm::begin([
	'action' => ['/editor/update-slide'],
	'options' => ['enctype' => 'multipart/form-data'],
]);
echo $form->field($editorModel, 'image')->fileInput();
echo $form->field($editorModel, 'text_size')->textInput();
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
$textSizeFieldId = Html::getInputId($editorModel, 'text_size');
$fileFieldId = Html::getInputId($editorModel, 'image');
$action = Url::to(['/editor/get-slide-by-index', 'story_id' => $model->id]);
$this->registerJs($js);
$js = <<< JS
    StoryEditor.initialize({
    	storyID: {$model->id},
    	getSlideAction: '$action',
    	textFieldID: '$textFieldId',
    	textSizeFieldID: '$textSizeFieldId',
    	fileFieldID: '$fileFieldId'
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
