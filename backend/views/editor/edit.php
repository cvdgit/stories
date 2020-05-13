<?php

use backend\assets\StoryEditorAsset;
use common\widgets\Reveal\Plugins\Video;
use common\widgets\RevealWidget;
use frontend\assets\PlyrAsset;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/** @var $this yii\web\View */
StoryEditorAsset::register($this);
PlyrAsset::register($this);
\backend\assets\CropperAsset::register($this);

/** @var $model common\models\Story */
$this->title = 'Редактор историй' . $model->title;
$this->params['sidebarMenuItems'] = [
    ['label' => 'История', 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
    ['label' => 'Озвучка', 'url' => ['audio/index', 'story_id' => $model->id]],
];

$storyID = $model->id;
$config = [
    'storyID' => $storyID,
    'getSlideAction' => Url::to(['/editor/load-slide', 'story_id' => $storyID]),
    'getSlideBlocksAction' => Url::to(['/editor/slide-blocks']),
    'getBlockFormAction' => Url::to(['/editor/form']),
    'createBlockAction' => Url::to(['/editor/create-block']),
    'newCreateBlockAction' => Url::to(['editor/block/create']),
    'deleteBlockAction' => Url::to(['/editor/delete-block']),
    'deleteSlideAction' => Url::to(['editor/delete-slide']),
    'currentSlidesAction' => Url::to(['editor/slides', 'story_id' => $storyID]),
    'slideVisibleAction' => Url::to(['editor/slide-visible']),
    'createSlideAction' =>  Url::to(['editor/create-slide', 'story_id' => $storyID]),
    'createSlideLinkAction' => Url::to(['editor/create-slide-link', 'story_id' => $storyID]),
    'slidesAction' => Url::to(['editor/slides']),
    'createSlideQuestionAction' => Url::to(['editor/create-slide-question', 'story_id' => $storyID]),
    'createNewSlideQuestionAction' => Url::to(['editor/new-create-slide-question']),
    'copySlideAction' => Url::to(['editor/copy-slide']),
    'storyImagesAction' => Url::to(['editor/image/list']),
];
$configJSON = Json::htmlEncode($config);

$slideSourceAction = Url::to(['editor/slide-source']);
$slideLinksAction = Url::to(['editor/links/index']);

$imagesConfigJSON = Json::htmlEncode([
    'addImagesAction' => Url::to(['editor/image/create']),
]);
$collectionConfigJSON = Json::htmlEncode([
    'setImageAction' => Url::to(['editor/image/set']),
    'accounts' => array_keys(Yii::$app->params['yandex.accounts']),
]);

$js = <<< JS

$('[data-toggle="tooltip"]').tooltip();
    
    StoryEditor.initialize($configJSON);
    ImageFromStory.init($imagesConfigJSON);
    StoryEditor.initCollectionsModule($collectionConfigJSON);

	$("#form-container")
	    .on("beforeSubmit", "form", StoryEditor.onBeforeSubmit)
	    .on("submit", "form", function(e) {
	        e.preventDefault();
	        return false;
	    });

	$("#slide-visible").on("click", function(e) {
	    e.preventDefault();
	    StoryEditor.toggleSlideVisible();
	});
	
	$("#slide-source").on("click", function(e) {
	    e.preventDefault();
	    StoryEditor.slideSourceModal("$slideSourceAction");
	});
	
	$("#slide-copy").on("click", function(e) {
	    e.preventDefault();
	    StoryEditor.copySlide();
	});
	
	$("#slide-links").on("click", function(e) {
	    e.preventDefault();
	    location.href = "$slideLinksAction&slide_id=" + StoryEditor.getCurrentSlideID();
	});
	
	$("#slide-block-params").on("click", ".show-block-params", function() {
	    $(".block-params", "#slide-block-params").removeClass("hide");
	    $(this).remove();
	});
	
	$('#save-slides-order').on('click', function(e) {
	    e.preventDefault();
	    StoryEditor.saveSlidesOrder();
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
            [
                'label' => 'Новый вопрос',
                'url' => '#',
                'linkOptions' => ['onclick' => 'StoryEditor.createSlideQuestion(); return false;'],
            ],
            [
                'label' => 'Вопросы (neo4j)',
                'url' => '#slide-new-question-modal',
                'linkOptions' => ['data-toggle' => 'modal'],
            ],
        ],
    ]
];

?>
<div class="row">
	<div class="col-lg-3">
        <h4>Слайды <a href="#" id="save-slides-order"><i class="glyphicon glyphicon-floppy-disk"></i></a> <div class="pull-right"><?= ButtonDropdown::widget($options) ?></div></h4>
        <div class="list-group" id="preview-container" style="margin-top: 20px"></div>
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
                        ['class' => Video::class, 'showControls' => true],
					],
		    	]) ?>
		    </div>
		</div>
        <div class="clearfix">
            <div class="editor-slide-actions pull-left">
                <?= Html::a('Ссылки', '#', ['id' => 'slide-links', 'style' => 'font-size: 18px']) ?>
                <?= Html::a('Связи', '#neo-relation-modal', ['data-toggle' => 'modal', 'style' => 'font-size: 18px']) ?>
            </div>
            <div class="editor-slide-actions pull-right">
                <a href="#" id="slide-copy" title="Копировать слайд"><i class="glyphicon glyphicon-copy"></i></a>
                <a href="#" id="slide-source" title="Код слайда"><i class="glyphicon glyphicon-fire"></i></a>
                <a href="#" id="slide-visible" title="Скрыть слайд"><i class="glyphicon"></i></a>
            </div>
        </div>
	</div>
</div>
<div class="row">
    <div class="col-md-3 text-center">
        <?= Html::a('Изображения', '#', ['class' => 'btn btn-primary', 'data-toggle' => 'modal', 'data-target' => '#story-images-modal']) ?>
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
            <div id="form-container"></div>
        </div>
    </div>
</div>

<div class="modal remote fade" id="slide-source-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content loader-lg"></div>
    </div>
</div>

<div class="modal fade" id="slide-link-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Создать ссылку на слайд</h4>
            </div>
            <div class="modal-body">
                <?= Html::dropDownList('linkStories',
                    null,
                    \common\helpers\StoryHelper::getStoryArray(),
                    ['prompt' => 'Выбрать историю', 'onchange' => 'StoryEditor.changeStory(this, "story-link-slides")', 'class' => 'form-control']) ?>
                <br>
                <?= Html::dropDownList('linkStorySlides',
                    null,
                    [],
                    ['prompt' => 'Выбрать слайд', 'id' => 'story-link-slides', 'class' => 'form-control']) ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="StoryEditor.link()">Создать ссылку</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="slide-question-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Выберите вопрос</h4>
            </div>
            <div class="modal-body">
                <?= Html::dropDownList('storyQuestionList',
                    null,
                    \common\models\StoryTestQuestion::questionArray(),
                    ['prompt' => 'Выбрать вопрос', 'class' => 'form-control', 'id' => 'story-question-list']) ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="StoryEditor.addQuestion()">Добавить вопрос</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="slide-collections-modal" style="z-index: 1051">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Коллекции</h4>
            </div>
            <div class="modal-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#story-collection" aria-controls="story-collection" role="tab" data-toggle="tab">Коллекции истории</a></li>
                        <li role="presentation"><a href="#yandex-collection" aria-controls="yandex-collection" role="tab" data-toggle="tab">Добавить из яндекс коллекции</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="story-collection">
                            <div class="collection_list" style="margin: 20px 0"></div>
                            <div class="row collection_card_list" style="margin-top: 20px"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="yandex-collection">
                            <div class="clearfix" style="padding-top: 20px">
                                <div class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            Аккаунт
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                            <?php foreach (array_keys(Yii::$app->params['yandex.accounts']) as $account): ?>
                                                <li><?= Html::a($account, '#', ['data-account' => $account]) ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <ul class="pagination pagination-lg" id="collection-page-list"></ul>
                            <div class="collection_list"></div>
                            <div class="row collection_card_list" style="margin-top: 20px"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="story-images-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Изображения истории</h4>
            </div>
            <div class="modal-body">
                <div class="story-images-list"></div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<?php
echo $this->render('modal/questions');
echo $this->render('modal/relations', ['model' => new \backend\models\NeoSlideRelationsForm()]);
echo $this->render('modal/crop');
echo $this->render('modal/new_image');
echo $this->render('modal/image_from_file', ['imageModel' => $imageModel]);
echo $this->render('modal/image_from_story');
echo $this->render('modal/image_from_url', ['imageModel' => $imageFromUrlModel]);
