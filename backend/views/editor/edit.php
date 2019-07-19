<?php

use backend\assets\StoryEditorAsset;
use common\widgets\RevealWidget;
use yii\helpers\Url;

/** @var $this yii\web\View */
StoryEditorAsset::register($this);

/** @var $model common\models\Story */
$this->title = 'Редактор историй' . $model->title;
$this->params['sidebarMenuItems'] = [
    ['label' => 'История', 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
];

$storyID = $model->id;
$action = Url::to(['/editor/get-slide-by-index', 'story_id' => $model->id]);
$blocksAction = Url::to(['/editor/get-slide-blocks', 'story_id' => $model->id]);
$formAction = Url::to(['/editor/form', 'story_id' => $model->id]);
$createBlockAction = Url::to(['/editor/create-block', 'story_id' => $model->id]);
$deleteBlockAction = Url::to(['/editor/delete-block', 'story_id' => $model->id]);
$deleteSlideAction = Url::to(['editor/delete-slide', 'story_id' => $model->id]);
$slidesAction = Url::to(['editor/slides', 'story_id' => $model->id]);
$slideVisibleAction = Url::to(['editor/slide-visible', 'story_id' => $storyID]);
$js = <<< JS
    
    StoryEditor.initialize({
        "storyID": "$storyID",
        "getSlideAction": "$action",
        "getSlideBlocksAction": "$blocksAction",
        "getBlockFormAction": "$formAction",
        "createBlockAction": "$createBlockAction",
        "deleteBlockAction": "$deleteBlockAction",
        "deleteSlideAction": "$deleteSlideAction",
        "slidesAction": "$slidesAction",
        "slideVisibleAction": "$slideVisibleAction"
    });

	$("#form-container")
	    .on("beforeSubmit", "form", StoryEditor.onBeforeSubmit)
	    .on("submit", "form", function(e) {
	        e.preventDefault();
	        return false;
	    });
	
	$("#preview-container").on("click", "a.remove-slide", function(e) {
	    e.preventDefault();
	    let slideIndex = $(this).parent().data("slideIndex");
	    StoryEditor.deleteSlide(slideIndex);
	});
	
	$("#slide-visible").on("click", function(e) {
	    e.preventDefault();
	    StoryEditor.toggleSlideVisible();
	});
JS;
$this->registerJs($js);
?>
<div class="row">
	<div class="col-lg-3">
        <h4>Слайды</h4>
		<div id="preview-container"></div>
	</div>
	<div class="col-lg-9">
		<div class="story-container">
			<div class="story-container-inner">
		    <?= RevealWidget::widget([
		    		'id' => 'story-editor',
		    		'initializeReveal' => false,
		    		'canViewStory' => true,
                    'options' => [
                        'history' => false,
                        'hash' => false,
                        'progress' => false,
                        'slideNumber' => false,
                    ],
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
        <div class="clearfix">
            <div class="pull-right" style="margin: 10px">
                <a href="#" id="slide-visible" title="Скрыть слайд"><i style="font-size: 32px" class="glyphicon"></i></a>
            </div>
        </div>
	</div>
</div>
<div class="row">
    <div class="col-lg-3">
        <?= $this->render('_blocks') ?>
    </div>
    <div class="col-lg-9">
        <h4>Параметры блока</h4>
        <div id="form-container"></div>
    </div>
</div>
