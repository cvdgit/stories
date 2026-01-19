<?php

declare(strict_types=1);

use backend\assets\MainAsset;
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
MainAsset::register($this);

$this->registerJs($this->renderFile("@backend/views/editor/_gpt_slide_text.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_pass_test.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_mental_map.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_gpt_rewrite_text.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_retelling.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_mental_map_common.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_mental_map_questions.js"));
$this->registerJs($this->renderFile("@backend/views/editor/mental-map/_create_ai.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_content_mental_map.js"));
$this->registerJs($this->renderFile("@backend/views/editor/_table_of_contents.js"));
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
            <ul class="slide-menu-list">
                <li class="slide-menu-item" data-slide-action="visible" title="Показать/Скрыть слайд">
                    <span class="toggle-slide-visible glyphicon glyphicon-eye-open"></span>
                </li>
                <li class="slide-menu-item" data-slide-action="images" title="Изображения истории">
                    <span class="glyphicon glyphicon-picture"></span>
                </li>
                <li class="slide-menu-item" data-slide-action="links" title="Ссылки">
                    <span class="glyphicon glyphicon-link"></span>
                </li>
                <li class="slide-menu-item" data-slide-action="relation" title="Связи Neo4j">
                    <span class="glyphicon glyphicon-transfer"></span>
                </li>
                <li class="slide-menu-item" data-slide-action="delete" title="Удалить слайд">
                    <span class="delete-slide glyphicon glyphicon-trash"></span>
                </li>
                <li class="slide-menu-item" data-slide-action="source" title="Исходный код слайда">
                    <span class="glyphicon glyphicon-wrench"></span>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="blocks-sidebars">
    <div class="blocks-sidebar hide visible">
        <ul>
            <li class="blocks-sidebar-item" data-block-type="table-of-contents">
                <span class="glyphicon glyphicon-tasks icon"></span>
                <span class="text">Оглавление</span>
            </li>
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
            <li class="blocks-sidebar-item" id="mental-map-item">
                <span class="glyphicon glyphicon-equalizer icon"></span>
                <span class="text">Ментальная карта</span>
            </li>
            <li class="blocks-sidebar-item" data-block-type="retelling">
                <span class="glyphicon glyphicon-book icon"></span>
                <span class="text">Пересказ</span>
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

<div class="modal rounded-0 fade" tabindex="-1" id="gpt-rewrite-text-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">Переписать текст</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body d-flex">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" style="max-width: 400px" id="select-prompts"></select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input id="gpt-prompt-name" autocomplete="off" type="text" class="form-control" placeholder="Название">
                            <div class="input-group-btn">
                                <button id="gpt-prompt-create" type="button" class="btn btn-primary">Создать</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="min-height: 400px">
                    <div>
                        <div style="cursor: pointer; user-select: none; font-weight: 500; margin: 10px 0" id="gpt-rewrite-text-prompt-toggle">Prompt</div>
                        <div id="gpt-rewrite-text-prompt-wrap" style="display: none;">
                            <div contenteditable="plaintext-only" style="margin-bottom: 20px; border: 1px #eee solid" class="textarea" id="gpt-rewrite-text-prompt"></div>
                            <div style="margin-bottom: 20px; display: flex; align-items: center">
                                <button style="margin-right: 20px" id="gpt-rewrite-text-with-prompt" type="button" class="btn btn-default disabled" disabled>Отправить запрос с измененным промтом</button>
                                <button style="margin-right: 10px;" id="gpt-rewrite-text-save-prompt" class="btn btn-success" type="button">Сохранить</button>
                                <div id="prompt-save-status" style="display: none">Успешно</div>
                            </div>
                        </div>
                    </div>
                    <div class="textarea" id="to-rewrite-text" style="max-height: 500px; overflow-y: auto" contenteditable="plaintext-only"></div>
                    <div id="gpt-rewrite-text-wrap" style="display: none; max-height: 500px; overflow-y: auto">
                        <div class="textarea" contenteditable="plaintext-only" id="gpt-rewrite-text-result">...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="gpt-rewrite-text-actions">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" id="gpt-rewrite-text">Отправить запрос</button>
                    <button style="display: none" type="button" class="btn btn-success" id="gpt-rewrite-text-save">Сохранить текст</button>
                </div>
                <div id="gpt-rewrite-text-loader" style="display: none; text-align: center">
                    <img src="/img/loading.gif" width="30" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal rounded-0 fade retelling-modal-template" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">...</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body d-flex">
                <div style="display: flex; flex-direction: row; column-gap: 20px">
                    <div style="flex: 1"></div>
                    <div style="flex: 1">
                        <label>
                            Пересказ с вопросами <input class="retelling-with-questions" type="checkbox">
                        </label>
                        <a class="retelling-questions-generate" style="display: none" href="">Сгенерировать вопросы</a>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row; column-gap: 20px">
                    <div style="flex: 1">
                        <textarea class="textarea retelling-slide-text" readonly style="width:100%; height: 500px; overflow-y: auto"></textarea>
                    </div>
                    <div style="flex: 1">
                        <div class="textarea retelling-questions" contenteditable="plaintext-only" style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; font-size: 14px"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="position: relative">
                <div>
                    <label>
                        Обязательный для прохождения <input checked class="retelling-required" type="checkbox">
                    </label>
                    <button type="button" class="btn btn-primary retelling-action">...</button>
                </div>
                <div class="retelling-loader" style="display: none; position:absolute; align-items: center; justify-content: center; left: 0; top: 0; width: 100%; height: 100%; background-color: #fff">
                    <img src="/img/loading.gif" width="30" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="modal rounded-0 fade" tabindex="-1" id="mental-map-questions-modal" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="display: flex; justify-content: space-between">
                    <h5 class="modal-title" style="margin-right: auto">...</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body d-flex">
                    <div style="display: flex; flex-direction: row; column-gap: 20px">
                        <div style="flex: 1"></div>
                        <div style="flex: 1">
                            <a id="mental-map-questions-generate" href="">Сгенерировать вопросы</a>
                        </div>
                    </div>
                    <div id="mental-map-questions-items" style="display: flex; flex-direction: column; row-gap: 20px"></div>
                </div>
                <div class="modal-footer" style="position: relative">
                    <div>
                        <label for="mental-map-questions-required">
                            Обязательный для прохождения <input checked id="mental-map-questions-required" type="checkbox">
                        </label>
                        <button type="button" class="btn btn-primary" id="mental-map-questions-action">...</button>
                    </div>
                    <div id="mental-map-questions-loader" style="display: none; position:absolute; align-items: center; justify-content: center; left: 0; top: 0; width: 100%; height: 100%; background-color: #fff">
                        <img src="/img/loading.gif" width="30" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
echo $this->render('modal/slide_link', ['storyModel' => $model]);
echo $this->render('modal/image_from_file', ['storyModel' => $model]);
echo $this->render('modal/image_from_url', ['storyModel' => $model]);
echo $this->render('modal/slide_images', ['storyModel' => $model]);
echo $this->render('modal/relations');
echo $this->render('modal/import_from_text');

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

    const editorRetelling = new EditorRetelling()
    const mentalMapQuestions = new MentalMapQuestions()

    var editorConfig = $configJSON;
    editorConfig.onBlockUpdate = function(block, action, element) {

        if (block.typeIsRetelling()) {
            editorRetelling.showUpdateModal({
                storyId: StoryEditor.getConfigValue('storyID'),
                slideId: StoryEditor.getCurrentSlide().getID(),
                blockId: block.getID()
            })
            return
        }

        if (block.typeIsMentalMapQuestions()) {
            mentalMapQuestions.showUpdateModal({
                storyId: StoryEditor.getConfigValue('storyID'),
                slideId: StoryEditor.getCurrentSlide().getID(),
                blockId: block.getID(),
                mentalMapId: $(block.getElement()).find('.mental-map').attr('data-mental-map-id')
            })
            return
        }

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

    editorConfig.onSlideLoad = (elem) => {
        const type = elem.find('[data-block-type]').attr('data-block-type');
        if (type === 'table-of-contents') {
            const payload = JSON.parse(elem.find('.table-of-contents-payload').text());
            const slidesMap = new Map();
            $('#slides-list > [data-slide-id]')
                .each((i, el) => slidesMap.set(Number($(el).attr('data-slide-id')), i + 1));
            TableOfContentsPlugin.initEdit(
                payload,
                elem.find('.table-of-contents'),
                slidesMap
            );
        }
    }

    const gptRewriteText = new GptRewriteText()
    editorConfig.gptRewriteHandler = (block, blockModifier) => {
        const content = getSlideTextContent(StoryEditor.getCurrentSlide())
        gptRewriteText.showModal({content, rewriteTextSaveHandler: (text) => {
            block.getElement().find('.slide-paragraph').html(text)
            blockModifier.change()
        }})
    }

    editorConfig.gptSpeechTrainer = async (block, blockModifier) => {

        const currentSlide = StoryEditor.getCurrentSlide();
        if (!currentSlide) {
            return;
        }
        const texts = getSlideTextContent(currentSlide)
        if (!texts.length) {
            toastr.warning("Текст на слайде не найден");
            return;
        }

        const currentSlideId = currentSlide.getID()

        const modal = new RemoteModal({
            id: 'create-content-mental-maps-modal',
            title: 'Речевой тренажер на основе контента'
        })

        modal.show({
            url: '/admin/index.php?r=editor/mental-map/content-form&slide_id=' + currentSlideId + '&block_id=' + block.getID(),
            callback: async (element) => {

                const contentMentalMap = new ContentMentalMap()
                const contentItems = window.contentItems || []
                const container = $(element).find('.content-mm-container')

                if (contentItems.length > 0) {
                    await contentMentalMap.updateFragments({
                        contentItems,
                        container,
                        text: texts,
                        onUpdateHandler: text => {
                            modal.hide()
                            /*block.getElement().find('.slide-paragraph').html(text)
                            blockModifier.change()*/
                        },
                        currentSlideId,
                        blockId: block.getID(),
                        onDeleteHandler: success => {
                            if (success) {
                                toastr.success('Успешно')
                            }
                            modal.hide()
                            StoryEditor.loadSlides(currentSlideId)
                        }
                    })
                    return
                }

                await contentMentalMap.createFragments({
                    currentSlideId,
                    blockId: block.getID(),
                    container,
                    text: texts,
                    onCreateHandler: text => {
                        modal.hide()
                        block.getElement().find('.slide-paragraph').html(text)
                        blockModifier.change()
                        StoryEditor.loadSlides(currentSlideId)
                    }
                })
            }
        })
    }

    editorConfig.mentalMapQuestionsHandler = (block, blockModifier) => {
        const id = $(block.getElement()).find('.mental-map').attr('data-mental-map-id')
        if (!id) {
            alert('Id not found')
            return
        }
        const currentSlide = StoryEditor.getCurrentSlide();
        if (!currentSlide) {
            toastr.error("Нет слайда");
            return;
        }
        mentalMapQuestions.showModal({
            storyId: StoryEditor.getConfigValue('storyID'),
            slideId: currentSlide.getID(),
            id
        })
    }

    StoryEditor.initialize(editorConfig);

    function initSelectStoryWidget(root) {
        var widget = $('.select-story-widget select.selectized', root);
        if (widget.length) {
            widget[0].selectize.trigger('change', widget.val());
        }
    }

    function getSlideContent(slide) {
        const texts = [];
        slide.getElement().find(`div[data-block-type="text"]`).map((i, el) => {
            const text = $(el).find(".slide-paragraph").html();
            if (text.length) {
                texts.push(text);
            }
        })
        return "<div>" + texts.join(`\\n`) + "</div>"
    }

    function getSlideTextContent(slide) {
        const texts = [];
        slide.getElement().find(`div[data-block-type="text"]`).map((i, el) => {
            const text = $(el).find(".slide-paragraph").text().trim();
            if (text.length) {
                texts.push(text);
            }
        })
        return texts.join(`\\n`)
    }

    function getSlideImages(slide) {
        const urls = [];
        slide.getElement().find(`div[data-block-type="image"]`).map((i, el) => {
            const src = $(el).find('img').attr('src');
            if (src.length) {
                urls.push(src);
            }
        })
        return urls
    }

    var editorPopover = new EditorPopover();
    const gpt = new GptSlideText();
    const passTest = new CreatePassTest();

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
        }},
        {name: "pass-question-test", title: "Создать тест с пропусками", click: function() {
            const currentSlide = StoryEditor.getCurrentSlide();
            if (!currentSlide) {
                toastr.error("Нет слайда");
                return;
            }
            const texts = getSlideContent(currentSlide)
            if (!texts.length) {
                toastr.warning("Текст на слайде не найден");
                return;
            }

            passTest.create({
                content: texts,
                slideId: currentSlide.getID(),
                storyId: StoryEditor.getConfigValue("storyID"),
                processCallback: () => {
                    StoryEditor.loadSlides();
                }
            })
        }}
    ]);
    editorPopover.attach('#mental-map-item', {placement: 'left'}, [
        {name: 'mental-map', title: 'Ментальная карта', click: function() {
            const currentSlide = StoryEditor.getCurrentSlide();
            if (!currentSlide) {
                toastr.error("Нет слайда");
                return;
            }
            const texts = getSlideTextContent(currentSlide)
            if (!texts.length) {
                toastr.warning("Текст на слайде не найден");
                return;
            }

            const images = getSlideImages(currentSlide)
            let image = ''
            if (images.length > 0) {
                image = images[0]
            }

            $('#create-block-modal')
                .off('loaded.bs.modal')
                .on('loaded.bs.modal', function() {
                    const storyId = StoryEditor.getConfigValue('storyID')
                    const slideId = currentSlide.getID()
                    attachBeforeSubmit($(this).find('form')[0], function(form) {
                        const formData = new FormData(form)
                        formData.append('MentalMapForm[texts]', texts)
                        formData.append('MentalMapForm[image]', image)
                            sendForm('/admin/index.php?r=editor/mental-map&current_slide_id=' + slideId + '&story_id=' + storyId, $(form).attr('method'), formData)
                                .done(response => {
                                    if (response && response?.success) {
                                        $('#create-block-modal').modal('hide')
                                      StoryEditor.loadSlides(response?.slide_id)
                                    } else {
                                      alert('error')
                                    }
                                });
                        });
                })
                .modal({'remote': StoryEditor.getCreateBlockUrl('mental_map')});
        }},
        {name: 'mental-maps-ai', title: 'Ментальные карты AI', click: function() {
            const currentSlide = StoryEditor.getCurrentSlide();
            if (!currentSlide) {
                return;
            }
            const texts = getSlideTextContent(currentSlide)
            if (!texts.length) {
                toastr.warning("Текст на слайде не найден");
                return;
            }

            const currentSlideId = currentSlide.getID()

            const modal = new RemoteModal({
                id: 'create-mental-maps-ai-modal',
                title: 'Ментальные карты AI'
            });

            modal.show({
              url: '/admin/index.php?r=editor/mental-map/create-ai-form&slide_id=' + currentSlideId,
              callback: function(responseBody) {
                const submitBtn = $(responseBody).find('button[type=submit]');
                formHelper.attachBeforeSubmit($(responseBody).find('form'), (form) => {
                  modalHelper.btnLoading(submitBtn);

                  const mentalMaps = [
                      {title: 'Ментальная карта', type: 'mental_map', fragments: []},
                      {title: 'Ментальная карта (четные пропуски)', type: 'mental_map_even_fragments', fragments: []},
                      {title: 'Ментальная карта (нечетные пропуски)', type: 'mental_map_odd_fragments', fragments: []}
                  ]

                  const mentalMapsAi = new MentalMapsAi()
                  mentalMapsAi.createMentalMaps(texts, (content) => {
                      const json = JSON.parse(content)
                      for (let i = 0; i < mentalMaps.length; i++) {
                        const type = mentalMaps[i].type
                          switch (type) {
                              case 'mental_map':
                                  json.map(textFragment => mentalMaps[i].fragments.push(textFragment))
                                  break;
                              case 'mental_map_even_fragments':
                                  json.map(textFragment => {
                                      const words = mentalMapsAi.processFragment(textFragment)
                                      mentalMaps[i].fragments.push(mentalMapsAi.hideWordsEven(words.words))
                                  })
                                  break;
                              case 'mental_map_odd_fragments':
                                  json.map(textFragment => {
                                      const words = mentalMapsAi.processFragment(textFragment)
                                      mentalMaps[i].fragments.push(mentalMapsAi.hideWordsOdd(words.words))
                                  })
                                  break;
                          }
                      }

                      const formData = new FormData(form[0])
                      formData.append('mentalMaps', JSON.stringify(mentalMaps))
                      formData.append('currentSlideId', currentSlideId)
                      formData.append('text', texts)

                      formHelper
                        .sendForm(form.attr('action'), form.attr('method'), formData)
                        .done(response => {
                            if (response && response.success) {
                                StoryEditor.loadSlides(response.slide_id);
                                modal.hide()
                            }
                            if (response && response.success === false) {
                                alert(response.message);
                            }
                        })
                        .always(() => modalHelper.btnReset(submitBtn));
                      })
                });
              }
            })
        }}
    ])
    editorPopover.attach('#create-slide-action', {'placement': 'right'}, [
        {'name': 'slide', 'title': 'Пустой слайд', 'click': StoryEditor.createSlide},
        {'name': 'copy', 'title': 'Копия текущего слайда', 'click': () => {
            const mentalMap = $(StoryEditor.getCurrentSlide().getElement()).find('.mental-map')
            if (mentalMap.length) {

                const modal = new RemoteModal({
                    id: 'copy-mental-map-modal',
                    title: 'Скопировать слайд с ментальной картой'
                });

                const mentalMapId = mentalMap.attr('data-mental-map-id')
                modal.show({
                  url: '/admin/index.php?r=editor/copy-slide/mental-map-form&id=' + mentalMapId + '&slide_id=' + StoryEditor.getCurrentSlide().getID(),
                  callback: function() {
                    const submitBtn = $(this).find('button[type=submit]');
                    formHelper.attachBeforeSubmit($(this).find('form'), (form) => {
                      modalHelper.btnLoading(submitBtn);
                      formHelper
                        .sendForm(form.attr('action'), form.attr('method'), new FormData(form[0]))
                        .done(response => {
                          if (response && response.success) {
                            StoryEditor.loadSlides(response.id);
                            modal.hide()
                          }
                          if (response && response.success === false) {
                            alert(response.message);
                          }
                        })
                        .always(() => modalHelper.btnReset(submitBtn));
                    });
                  }
                })

                return
            }
            const retelling = $(StoryEditor.getCurrentSlide().getElement()).find('.retelling-block')
            if (retelling.length) {

                const modal = new RemoteModal({
                    id: 'copy-retelling-modal',
                    title: 'Скопировать слайд с пересказом'
                });

                const retellingId = retelling.attr('data-retelling-id')
                modal.show({
                  url: '/admin/index.php?r=editor/copy-slide/retelling-form&id=' + retellingId + '&slide_id=' + StoryEditor.getCurrentSlide().getID(),
                  callback: function() {
                    const submitBtn = $(this).find('button[type=submit]');
                    formHelper.attachBeforeSubmit($(this).find('form'), (form) => {
                      modalHelper.btnLoading(submitBtn);
                      formHelper
                        .sendForm(form.attr('action'), form.attr('method'), new FormData(form[0]))
                        .done(response => {
                          if (response && response.success) {
                            StoryEditor.loadSlides(response.id);
                            modal.hide()
                          }
                          if (response && response.success === false) {
                            alert(response.message);
                          }
                        })
                        .always(() => modalHelper.btnReset(submitBtn));
                    });
                  }
                })

                return
            }
            StoryEditor.copySlide()
        }},
        {'name': 'link', 'title': 'Ссылка на слайд', 'click': function() {
            $('#slide-link-modal').modal('show');
        }},
        {name: 'importFromText', title: 'Импортировать из текста', click: () => {
            $('#import-from-text-modal').modal('show')
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
        const type = $(this).attr('data-block-type');

        if (type === 'text') {
            const html = StoryEditor.createEmptyBlock(type);
            StoryEditor.createSlideBlock(html);
            return
        }

        if (type === 'table-of-contents') {
            const html = StoryEditor.createTableOfContentsBlock();
            const id = StoryEditor.createSlideBlock(html);
            const block = StoryEditor.findBlockByID(id);
            const payload = JSON.parse(block.getElement().find('.table-of-contents-payload').text());
            TableOfContentsPlugin.initEdit(
                payload,
                block.getElement().find('.table-of-contents')
            );
            return;
        }

        if (type === 'retelling') {

            const currentSlide = StoryEditor.getCurrentSlide();
            if (!currentSlide) {
                toastr.error("Нет слайда");
                return;
            }
            const texts = getSlideTextContent(currentSlide)
            if (!texts.length) {
                toastr.warning("Текст на слайде не найден");
                return;
            }

            editorRetelling.showModal({
                storyId: StoryEditor.getConfigValue('storyID'),
                slideId: currentSlide.getID(),
                texts
            })
            return
        }

        showCreateBlockModal(type);
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
        } else {
            elem.prop('data-process', false);
        }
    });

    $('[data-editor-show=slide]').on('click', function() {
        window.open(StoryEditor.getSlidePreviewUrl(), 'target=_blank');
    });

    $('#story-editor').on('click', '[data-retelling-action=update]', e => {
        e.preventDefault()
        const blockId = $(e.target).parents('.sl-block').attr('data-block-id')
        if (blockId) {
            editorRetelling.showUpdateModal({
                storyId: StoryEditor.getConfigValue('storyID'),
                slideId: StoryEditor.getCurrentSlide().getID(),
                blockId
            })
        }
    })

    $('#story-editor').on('click', '[data-mental-map-action=update-questions]', e => {
        e.preventDefault()
        const blockId = $(e.target).parents('.sl-block').attr('data-block-id')
        const mentalMapId = $(e.target).parent().attr('data-mental-map-id')
        if (!mentalMapId) {
            alert('Id not found')
            return
        }
        if (blockId) {
            mentalMapQuestions.showUpdateModal({
                storyId: StoryEditor.getConfigValue('storyID'),
                slideId: StoryEditor.getCurrentSlide().getID(),
                blockId,
                mentalMapId
            })
        }
    })

    const tableOfContents = new TableOfContents();
    $('#story-editor').on('click', '[data-block-type=table-of-contents] .table-of-contents-edit', ({preventDefault, target}) => {
        preventDefault();

        const elem = $(target).parents('.table-of-contents');
        const payload = JSON.parse(elem.find('.table-of-contents-payload').text());
        const updatePayloadHandler = (p) => {
            elem.find('.table-of-contents-payload').text(JSON.stringify(p));
            StoryEditor.change();
            TableOfContentsPlugin.initEdit(p, elem);
        }

        fetch('/admin/index.php?r=editor/slides&story_id=' + StoryEditor.getConfigValue('storyID'), {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
            }
        })
            .then(response => response.json())
            .then(slides => {
                tableOfContents.show(
                    payload,
                    slides
                        .filter(s => !s.haveTableOfContents)
                        .filter(s => !s.isHidden)
                        .sort((a, b) => a.slideNumber - b.slideNumber),
                    updatePayloadHandler
                );
            });
    })
})();
JS;
$this->registerJs($js);
