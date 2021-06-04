<?php
use backend\assets\CropperAsset;
use backend\assets\StoryEditorAsset;
use backend\widgets\BackendRevealWidget;
use frontend\assets\PlyrAsset;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
/** @var $this yii\web\View */
/** @var $localTestForm backend\models\editor\LocalTestForm */
/** @var $remoteTestForm backend\models\editor\RemoteTestForm */
StoryEditorAsset::register($this);
PlyrAsset::register($this);
CropperAsset::register($this);
/** @var $model common\models\Story */
$this->title = 'Редактор: ' . $model->title;
$this->params['breadcrumbs'] = [
    ['label' => 'Список историй', 'url' => ['story/index']],
    ['label' => $model->title, 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias]), 'target' => '_blank'],
    $this->title,
];
$this->params['sidebarMenuItems'] = [
    ['label' => $model->title, 'url' => ['story/update', 'id' => $model->id]],
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
    'label' => '<span class="glyphicon glyphicon-plus"></span> Новый слайд&nbsp;',
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
                'label' => 'Тест из neo4j',
                'url' => '#slide-new-question-modal',
                'linkOptions' => ['data-toggle' => 'modal'],
            ],
            [
                'label' => 'Тест',
                'url' => '#new-test-modal',
                'linkOptions' => ['data-toggle' => 'modal'],
            ],
        ],
    ]
];

?>

    <div class="wrap-editor">
        <div class="slides-sidebar">
            <div class="slides-actions">
                <button id="create-slide-action">Новый слайд</button>
                <button id="slide-copy">Копировать</button>
            </div>
            <div class="list-group slides-container" id="preview-container"></div>
        </div>
        <div class="wrap-editor-main">
            <div class="reveal-viewport">
                <?= BackendRevealWidget::widget(['id' => 'story-editor']) ?>
            </div>
            <div class="slide-menu">
                <ul style="margin: 0; padding: 0; list-style: none">
                    <li class="slide-menu-item">
                        <span class="toggle-slide-visible glyphicon glyphicon-eye-open" data-toggle="tooltip" title="Показать слайд"></span>
                    </li><!--
                    --><li class="slide-menu-item">
                        <span class="delete-slide glyphicon glyphicon-trash" data-toggle="tooltip" title="Удалить слайд"></span>
                    </li><!--
                    --><li class="slide-menu-item">
                        <span class="glyphicon glyphicon-picture" data-toggle="modal" title="Изображения истории" data-target="#story-images-modal"></span>
                    </li><!--
                    --><li class="slide-menu-item">
                        <span class="glyphicon glyphicon-link" id="slide-links" title="Ссылки"></span>
                    </li><!--
                    --><li class="slide-menu-item">
                        <span class="glyphicon glyphicon-transfer" data-toggle="modal" data-target="#neo-relation-modal" title="Связи Neo4j"></span>
                    </li><!--
                    --><li class="slide-menu-item">
                        <span class="glyphicon glyphicon-wrench" id="slide-source" title="Исходный код слайда"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="blocks-sidebars">
        <div class="blocks-sidebar visible">
            <ul>
                <li class="blocks-sidebar-item" data-block-type="text">
                    <span class="glyphicon glyphicon-text-size icon"></span>
                    <span class="text">Текст</span>
                </li>
                <li class="blocks-sidebar-item">
                    <span class="glyphicon glyphicon-picture icon"></span>
                    <span class="text">Картинка</span>
                </li>
                <li class="blocks-sidebar-item" id="create-video-block">
                    <span class="glyphicon glyphicon-facetime-video icon"></span>
                    <span class="text">Видео</span>
                </li>
                <li class="blocks-sidebar-item">
                    <span class="glyphicon glyphicon-education icon"></span>
                    <span class="text">Тест</span>
                </li>
                <li class="blocks-sidebar-item" data-block-type="button">
                    <span class="glyphicon glyphicon-play icon"></span>
                    <span class="text">Кнопка</span>
                </li>
            </ul>
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

<div class="modal remote fade" id="create-block-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="update-block-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
echo $this->render('modal/questions', ['model' => $remoteTestForm]);
echo $this->render('modal/relations', ['model' => new \backend\models\NeoSlideRelationsForm()]);
echo $this->render('modal/crop');
echo $this->render('modal/new_image');
echo $this->render('modal/image_from_file', ['imageModel' => $imageModel]);
echo $this->render('modal/image_from_story');
echo $this->render('modal/image_from_url', ['imageModel' => $imageFromUrlModel]);
echo $this->render('modal/new_test', ['model' => $localTestForm]);

$js = <<< JS

var editorPopover = new EditorPopover();

editorPopover.attach('#create-video-block', {'placement': 'left'}, [
    {'name': 'youtube', 'title': 'YouTube', 'click': function() {alert('youtube')}},
    {'name': 'file', 'title': 'Из файла', 'click': function() {alert('file')}}
]);

editorPopover.attach('li[data-block-type=button]', {'placement': 'left'}, [
    {'name': 'test', 'title': 'Тест', 'click': function() {alert('test')}},
    {'name': 'transition', 'title': 'Переход', 'click': function() {alert('transition')}}
]);

editorPopover.attach('#create-slide-action', {'placement': 'right'}, [
    {'name': 'slide', 'title': 'Пустой слайд', 'click': StoryEditor.createSlide},
    {'name': 'link', 'title': 'Ссылка на слайд', 'click': StoryEditor.createSlideLink}
]);

$('body')
.on('click', function(e) {
    $('[data-original-title]').each(function() {
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            var popoverElement = $(this).data('bs.popover').tip();
            var popoverWasVisible = popoverElement.is(':visible');
            if (popoverWasVisible) {
                $(this).popover('hide');
                $(this).click();
            }
        }
    });
}).on('hidden.bs.popover', function(e) {
    $(e.target).data("bs.popover").inState = {click: false, hover: false, focus: false};
});

$('#story-editor').on('dblclick', 'div.sl-block', function(e) {
    var type = $(this).attr('data-block-type'),
        id = $(this).attr('data-block-id');
    $('#update-block-modal')
        .modal({'remote': StoryEditor.getUpdateBlockUrl(id)});
});

$('#create-block-modal, #update-block-modal').on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
});

$('.blocks-sidebar').on('click', '[data-block-type]', function() {
    $('#create-block-modal')
        .modal({'remote': StoryEditor.getCreateBlockUrl($(this).attr('data-block-type'))});
});

JS;
$this->registerJs($js);