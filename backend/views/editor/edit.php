<?php
use backend\assets\CropperAsset;
use backend\assets\StoryEditorAsset;
use backend\widgets\BackendRevealWidget;
use frontend\assets\PlyrAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
/** @var $this yii\web\View */
StoryEditorAsset::register($this);
PlyrAsset::register($this);
CropperAsset::register($this);
/** @var $model common\models\Story */
$this->title = 'Редактор: ' . $model->title;
?>
<div class="wrap-editor">
    <div class="slides-sidebar">
        <div class="slides-actions">
            <div class="action-group">
                <?= Html::button('<i class="glyphicon glyphicon-home"></i>', [
                    'title' => 'Вернуться к редактированию истории',
                    'onclick' => 'location.href = "' . Url::to(['story/update', 'id' => $model->id]) . '"'
                ]) ?>
                <?= Html::button('<i class="glyphicon glyphicon-eye-open"></i>', [
                    'title' => 'Просмотр истории',
                    'data-editor-show' => 'slide',
                ]) ?>
            </div>
            <button id="create-slide-action">Новый слайд</button>
            <button id="save-data">
                <i class="glyphicon glyphicon-ok"></i>
            </button>
        </div>
        <div class="list-group slides-container" id="slides-list"></div>
    </div>
    <div class="wrap-editor-main">
        <div class="reveal-viewport">
            <?= BackendRevealWidget::widget(['id' => 'story-editor']) ?>
        </div>
        <div class="slide-menu" style="display: none">
            <ul style="margin: 0; padding: 0; list-style: none">
                <li class="slide-menu-item" data-slide-action="visible" title="Показать/Скрыть слайд">
                    <span class="toggle-slide-visible glyphicon glyphicon-eye-open"></span>
                </li><!--
                --><li class="slide-menu-item" data-slide-action="images" title="Изображения истории">
                    <span class="glyphicon glyphicon-picture"></span>
                </li><!--
                --><li class="slide-menu-item" data-slide-action="links" title="Ссылки">
                    <span class="glyphicon glyphicon-link"></span>
                </li><!--
                --><li class="slide-menu-item" data-slide-action="relation" title="Связи Neo4j">
                    <span class="glyphicon glyphicon-transfer"></span>
                </li><!--
                --><li class="slide-menu-item" data-slide-action="delete" title="Удалить слайд">
                    <span class="delete-slide glyphicon glyphicon-trash"></span>
                </li><!--
                --><li class="slide-menu-item" data-slide-action="source" title="Исходный код слайда">
                    <span class="glyphicon glyphicon-wrench"></span>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="blocks-sidebars">
    <div class="blocks-sidebar hide visible">
        <ul>
            <li class="blocks-sidebar-item" data-block-type="text">
                <span class="glyphicon glyphicon-text-size icon"></span>
                <span class="text">Текст</span>
            </li>
            <li class="blocks-sidebar-item" id="create-image-block">
                <span class="glyphicon glyphicon-picture icon"></span>
                <span class="text">Картинка</span>
            </li>
            <li class="blocks-sidebar-item" id="create-video-block">
                <span class="glyphicon glyphicon-facetime-video icon"></span>
                <span class="text">Видео</span>
            </li>
            <li class="blocks-sidebar-item" data-block-type="html">
                <span class="glyphicon glyphicon-education icon"></span>
                <span class="text">Тест</span>
            </li>
            <li class="blocks-sidebar-item" id="create-button-block">
                <span class="glyphicon glyphicon-play icon"></span>
                <span class="text">Кнопка</span>
            </li>
        </ul>
    </div>
</div>
<div class="hide" id="save-container"></div>
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
echo $this->render('modal/slide_link', ['storyModel' => $model]);
echo $this->render('modal/image_from_file', ['storyModel' => $model]);
echo $this->render('modal/image_from_url', ['storyModel' => $model]);
echo $this->render('modal/slide_images', ['storyModel' => $model]);
echo $this->render('modal/relations');
echo $this->render('modal/slide_source');

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
    'storyUrl' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias]),
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
(function() {
    
    var editorConfig = $configJSON;
    editorConfig.onBlockUpdate = function(block, action, element) {
        var modal = $('#update-block-modal');
        if (block.typeIsVideo()) {
            modal.find('.modal-dialog').addClass('modal-xl');
        }
        else {
            modal.find('.modal-dialog').removeClass('modal-xl');
        }
        modal
            .off('loaded.bs.modal')
            .on('loaded.bs.modal', function() {
                if (block.typeIsVideo()) {
                    $(this).find('#video-preview').attr('data-id', '123').append($(element).find('div.wikids-video-player'));
                    WikidsVideo.reset();
                    WikidsVideo.createPlayer($(this).find('#video-preview'));
                }
                initSelectStoryWidget(this);
            });
        modal.modal({'remote': action});
    };
    editorConfig.onInit = function() {
    }
    StoryEditor.initialize(editorConfig);
    
    function initSelectStoryWidget(root) {
        var widget = $('.select-story-widget select.selectized', root);
        if (widget.length) {
            widget[0].selectize.trigger('change', widget.val());
        }
    }

    var editorPopover = new EditorPopover();
    editorPopover.attach('#create-video-block', {'placement': 'left'}, [
        {'name': 'youtube', 'title': 'YouTube', 'click': function() {
            showCreateBlockModal('video');
        }},
        {'name': 'file', 'title': 'Из файла', 'click': function() {
            showCreateBlockModal('videofile');
        }}
    ]);
    editorPopover.attach('#create-button-block', {'placement': 'left'}, [
        {'name': 'test', 'title': 'Тест', 'click': function() {
            showCreateBlockModal('test');
        }},
        {'name': 'transition', 'title': 'Переход', 'click': function() {
            showCreateBlockModal('transition');
        }}
    ]);
    editorPopover.attach('#create-slide-action', {'placement': 'right'}, [
        {'name': 'slide', 'title': 'Пустой слайд', 'click': StoryEditor.createSlide},
        {'name': 'copy', 'title': 'Копия текущего слайда', 'click': StoryEditor.copySlide},
        {'name': 'link', 'title': 'Ссылка на слайд', 'click': function() {
            $('#slide-link-modal').modal('show');
        }}
    ]);
    editorPopover.attach('#create-image-block', {'placement': 'left'}, [
        {'name': 'from_file', 'title': 'Из файла', 'click': function() {
            $('#image-from-file-modal')
                .on('show.bs.modal', function() {
                    $('#imagefromfileform-slide_id', this).val(StoryEditor.getCurrentSlideID());
                })
                .modal('show');
        }},
        {'name': 'from_url', 'title': 'Из ссылки', 'click': function() {
            $('#image-from-url-modal')
                .on('show.bs.modal', function() {
                    $('#imagefromurlform-slide_id', this).val(StoryEditor.getCurrentSlideID());
                })
                .modal('show');
        }}
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
        })
        .on('hidden.bs.popover', function(e) {
            $(e.target).data("bs.popover").inState = {click: false, hover: false, focus: false};
        });
    
    $('#create-block-modal, #update-block-modal').on('hide.bs.modal', function() {
        if ($(this).find('#video-preview').length) {
            WikidsVideo.destroyPlayers();
        }
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });
    
    function showCreateBlockModal(type) {
        try {
            $('#create-block-modal')
                .off('loaded.bs.modal')
                .on('loaded.bs.modal', function() {
                    initSelectStoryWidget(this);
                })
                .modal({'remote': StoryEditor.getCreateBlockUrl(type)});
        }
        catch (e) {
            toastr.error(e.error());
        }
    }

    $('.blocks-sidebar').on('click', '[data-block-type]', function() {
        showCreateBlockModal($(this).attr('data-block-type'));
    });
    
    $('.slide-menu').on('click', '[data-slide-action]', function(e) {
        var elem = $(this);
        if (elem.prop('data-process')) {
            return;
        }
        elem.prop('data-process', true);
        var action = $(this).attr('data-slide-action');
        var callback;
        switch (action) {
            case 'delete':
                if (!confirm('Удалить слайд?')) {
                    return;
                }
                callback = StoryEditor.deleteSlide;
                break;
            case 'visible':
                callback = StoryEditor.slideVisibleToggle;
                break;
            case 'images':
                $('#story-images-modal').modal('show');
                break;
            case 'links':
                location.href = '/admin/index.php?r=editor/links/index&slide_id=' + StoryEditor.getCurrentSlideID();
                break;
            case 'relation':
                $('#neo-relation-modal').modal('show');
                break;
            case 'source':
                $('#slide-source-modal').modal('show');
                break;
        }
        if (callback) {
            callback().always(function() {
                elem.prop('data-process', false);
            });
        }
        else {
            elem.prop('data-process', false);
        }
    });
    
    $('[data-editor-show=slide').on('click', function() {
        window.open(StoryEditor.getSlidePreviewUrl(), 'target=_blank');
    });
})();
JS;
$this->registerJs($js);
