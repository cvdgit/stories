<?php

declare(strict_types=1);

use backend\assets\json\JsonPatchAsset;
use backend\assets\StoryEditorAsset;
use backend\widgets\BackendRevealWidget;
use common\models\Story;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var Story $model
 * @var string $configJSON
 * @var bool $inLesson
 */

StoryEditorAsset::register($this);
JsonPatchAsset::register($this);
$this->registerJs($this->renderFile("@backend/views/editor/_gpt_slide_text.js"));
?>
<div class="wrap-editor">
    <div class="slides-sidebar">
        <div class="slides-actions">
            <div class="action-group">
                <div class="button__wrap">
                    <?= Html::button('<i class="glyphicon glyphicon-home"></i>', [
                        'title' => 'Вернуться к редактированию истории',
                        'onclick' => 'location.href = "' . Url::to(['story/update', 'id' => $model->id]) . '"'
                    ]) ?>
                </div>
                <div class="button__wrap">
                    <?= Html::button('<i class="glyphicon glyphicon-eye-open"></i>', [
                        'title' => 'Просмотр истории',
                        'data-editor-show' => 'slide',
                    ]) ?>
                </div>
            </div>
            <button id="create-slide-action">Новый слайд</button>
            <?php if (!$inLesson): ?>
            <?= Html::button('Разделы', [
                'title' => 'Управление разделами',
                'onclick' => 'location.href = "' . Url::to(['course/update', 'id' => $model->id]) . '"'
            ]) ?>
            <?php endif ?>
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
            <li class="blocks-sidebar-item" id="gpt-text">
                <img src="/img/chatgpt-icon.png" width="28" alt="">
                <span class="text">Тест</span>
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
//echo $this->render('modal/slide_source');

$storyID = $model->id;

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
    editorConfig.onImageReplace = function(blockID) {
        $('#story-images-modal')
            .data('blockId', blockID)
            .modal('show');
    }
    editorConfig.onInit = function() {
    }
    editorConfig.onReady = function() {
        $('.page-loader').addClass('loaded');
    }
    StoryEditor.initialize(editorConfig);

    function initSelectStoryWidget(root) {
        var widget = $('.select-story-widget select.selectized', root);
        if (widget.length) {
            widget[0].selectize.trigger('change', widget.val());
        }
    }

    var editorPopover = new EditorPopover();
    const gpt = new GptSlideText();
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
    editorPopover.attach('#gpt-text', {'placement': 'left'}, [
        {'name': 'gpt-slide-text', 'title': 'Создать тест', 'click': function() {
            const currentSlide = StoryEditor.getCurrentSlide();
            if (!currentSlide) {
                toastr.error("Нет слайда");
                return;
            }
            const texts = [];
            currentSlide.getElement().find(`div[data-block-type="text"]`).map((i, el) => {
                const text = $(el).text();
                if (text.length) {
                    texts.push(text);
                }
            })

            if (!texts.length) {
                toastr.warning("Текст на слайде не найден");
                return;
            }

            gpt.showModal({
                content: texts.join(`\\n`),
                slideId: currentSlide.getID(),
                storyId: StoryEditor.getConfigValue("storyID"),
                processCallback: () => {
                    StoryEditor.loadSlides();
                }
            });
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
        }},
        {'name': 'manager', 'title': 'Менеджер', 'click': function() {
            $('#story-images-modal')
                .data('mode', 'insert')
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
        var type = $(this).attr('data-block-type');
        if (type === 'text') {
            var html = StoryEditor.createEmptyBlock(type);
            StoryEditor.createSlideBlock(html);
        }
        else {
            showCreateBlockModal(type);
        }
    });

    const slideSourceModal = new RemoteModal({
        id: 'slide-source-modal',
        title: 'Разметка слайда',
        dialogClassName: 'modal-lg'
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
                slideSourceModal.show({
                    url: '/admin/index.php?r=slide/source&id=' + StoryEditor.getCurrentSlideID(),
                    callback: function() {
                        attachBeforeSubmit($(this).find('form')[0], function(form) {
                            sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
                                .done(response => {
                                    StoryEditor.loadSlide(StoryEditor.getCurrentSlideID());
                                    slideSourceModal.hide();
                                });
                        });
                    }
                });
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

    $('[data-editor-show=slide]').on('click', function() {
        window.open(StoryEditor.getSlidePreviewUrl(), 'target=_blank');
    });
})();
JS;
$this->registerJs($js);
