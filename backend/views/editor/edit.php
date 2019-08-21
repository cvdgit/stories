<?php

use backend\assets\StoryEditorAsset;
use common\widgets\RevealWidget;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Json;
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
$config = [
    'storyID' => $storyID,
    'getSlideAction' => Url::to(['/editor/load-slide', 'story_id' => $storyID]),
    'getSlideBlocksAction' => Url::to(['/editor/slide-blocks']),
    'getBlockFormAction' => Url::to(['/editor/form']),
    'createBlockAction' => Url::to(['/editor/create-block']),
    'deleteBlockAction' => Url::to(['/editor/delete-block']),
    'deleteSlideAction' => Url::to(['editor/delete-slide']),
    'currentSlidesAction' => Url::to(['editor/slides', 'story_id' => $storyID]),
    'slideVisibleAction' => Url::to(['editor/slide-visible']),
    'createSlideAction' =>  Url::to(['editor/create-slide', 'story_id' => $storyID]),
    'createSlideLinkAction' => Url::to(['editor/create-slide-link', 'story_id' => $storyID]),
    'slidesAction' => Url::to(['editor/slides']),
];
$configJSON = Json::htmlEncode($config);

$slideSourceAction = Url::to(['editor/slide-source']);

$js = <<< JS
    
    StoryEditor.initialize($configJSON);

	$("#form-container")
	    .on("beforeSubmit", "form", StoryEditor.onBeforeSubmit)
	    .on("submit", "form", function(e) {
	        e.preventDefault();
	        return false;
	    });
	
	$("#preview-container").on("click", "a.remove-slide", function(e) {
	    e.preventDefault();
	    let slideID = $(this).parent().data("slideId");
	    StoryEditor.deleteSlide(slideID);
	});
	
	$("#slide-visible").on("click", function(e) {
	    e.preventDefault();
	    StoryEditor.toggleSlideVisible();
	});
	
	$("#slide-source").on("click", function(e) {
	    e.preventDefault();
	    StoryEditor.slideSourceModal("$slideSourceAction");
	});
JS;
$this->registerJs($js);

$options = [
    'encodeLabel' => false,
    'label' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
    'options' => [
        'class' => 'btn-sm btn-default',
        'title' => 'Добавить слайд',
    ],
    'dropdown' => [
        'items' => [
            [
                'label' => 'Новый слайд',
                'url' => '#',
                'linkOptions' => ['onclick' => 'StoryEditor.createSlide(); return false;'],
            ],
            [
                'label' => 'Ссылка на слайд',
                'url' => '#',
                'linkOptions' => ['onclick' => 'StoryEditor.createSlideLink(); return false;'],
            ],
        ],
    ]
];

?>
<div class="row">
	<div class="col-lg-3">
        <h4>Слайды <div class="pull-right"><?= ButtonDropdown::widget($options) ?></div></h4>
        <div id="preview-container" style="margin-top: 20px"></div>
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
                <a href="#" id="slide-source" title="Код слайда" style="margin-right: 10px"><i style="font-size: 32px" class="glyphicon glyphicon-fire"></i></a>
                <a href="#" id="slide-visible" title="Скрыть слайд"><i style="font-size: 32px" class="glyphicon"></i></a>
            </div>
        </div>
	</div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div id="slide-blocks">
            <?= $this->render('_blocks') ?>
        </div>
    </div>
    <div class="col-lg-9">
        <div id="slide-block-params">
            <h4>Параметры блока</h4>
            <div id="form-container"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="slide-link-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Создать ссылку на слайд</h4>
            </div>
            <div class="modal-body">
                <?= Html::dropDownList('linkStories', null, \common\helpers\StoryHelper::getStoryArray(), ['prompt' => 'Выбрать историю', 'onchange' => 'StoryEditor.changeStory(this)']) ?>
                <?= Html::dropDownList('linkStorySlides', null, [], ['pormpt' => 'Выбрать слайд', 'id' => 'story-link-slides']) ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="StoryEditor.link()">Создать ссылку</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
