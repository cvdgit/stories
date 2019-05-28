<?php

use yii\helpers\Html;
use backend\assets\StoryEditorAsset;
use common\widgets\RevealWidget;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $model common\models\Story */
/** @var $story backend\components\story\Story */

StoryEditorAsset::register($this);

$this->title = 'Редактор историй' . $model->title;
$this->params['sidebarMenuItems'] = [
    ['label' => 'История', 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
];

$action = Url::to(['/editor/get-slide-by-index', 'story_id' => $model->id]);
$blocksAction = Url::to(['/editor/get-slide-blocks', 'story_id' => $model->id]);
$formAction = Url::to(['/editor/form', 'story_id' => $model->id]);
$createBlockAction = Url::to(['/editor/create-block', 'story_id' => $model->id]);
$deleteBlockAction = Url::to(['/editor/delete-block', 'story_id' => $model->id]);
$js = <<< JS
    
    StoryEditor.initialize({
        "getSlideAction": "$action",
        "getSlideBlocksAction": "$blocksAction",
        "getBlockFormAction": "$formAction",
        "createBlockAction": "$createBlockAction",
        "deleteBlockAction": "$deleteBlockAction"
    });
    let slideIndex = StoryEditor.readUrl() || 0;
	StoryEditor.loadSlide(slideIndex, true);
	
	$("#form-container")
	    .on("beforeSubmit", "form", StoryEditor.onBeforeSubmit)
	    .on("submit", "form", function(e) {
	        e.preventDefault();
	        return false;
	    });
JS;
$this->registerJs($js);
?>
<div class="row">
	<div class="col-xs-3">
        <h4>Слайды</h4>
		<div id ="preview-container" style="overflow: auto">
		<?php foreach ($story->getSlides() as $slide): ?>
		<?php $slideIndex = $slide->getSlideNumber() - 1; ?>
			<div class="img-thumbnail preview-container-item" style="height: 80px; width: 80px; margin-bottom: 10px;" data-slide-index="<?= $slideIndex ?>">
			<?= Html::a("Слайд {$slideIndex}", '#', ['class' => '', 'onclick' => 'StoryEditor.loadSlide(' . $slideIndex . ', true); return false']) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
	<div class="col-xs-9">
		<div class="story-container">
			<div class="story-container-inner">
		    <?= RevealWidget::widget([
		    		'id' => 'story-editor',
		    		'initializeReveal' => false,
		    		'canViewStory' => true,
                    'assets' => [
                        \backend\assets\RevealAsset::class,
                        \backend\assets\WikidsRevealAsset::class,
                    ],
		    		'plugins' => [
                        [
                            'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
                            'buttons' => [
                                new \common\widgets\RevealButtons\FullscreenButton(),
                            ],
                        ],
					],
		    	]) ?>
		    </div>
		</div>
	</div>
</div>
<div class="row">
    <div class="col-xs-3">
        <?= $this->render('_blocks') ?>
    </div>
    <div class="col-xs-9">
        <h4>Параметры блока</h4>
        <div id="form-container"></div>
    </div>
</div>
