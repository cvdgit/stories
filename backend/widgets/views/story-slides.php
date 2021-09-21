<?php
use backend\widgets\SelectStoryWidget;
use yii\helpers\Html;
$css = <<<CSS
.thumb-reveal {
    position: relative;
    top: 0;
    left: 0;
    z-index: 2;
    -webkit-transform-origin: 0% 0%;
    transform-origin: 0% 0%;
}
.thumb-reveal section {
    border: 0 !important;
    outline: 0 !important;
    display: block !important;
}
.thumb-reveal-wrapper {
    position: relative;
    height: 207px;
    width: 400px;
    margin-bottom: 10px;
}
.thumb-reveal-inner {
    width: 100%;
    overflow: hidden;
    border: 1px solid #eee;
    height: 205px;
}
.thumb-reveal-wrapper:before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 10;
}
.thumb-reveal-options {
    position: absolute;
    right: 6px;
    bottom: 6px;
    z-index: 101;
    visibility: hidden;
}
.thumb-reveal-info {
    position: absolute;
    right: 6px;
    top: 6px;
    z-index: 101;
}
.thumb-reveal-wrapper:hover .thumb-reveal-options {
    visibility: visible;
}
.thumb-reveal-wrapper:hover .thumb-reveal-inner {
    border-color: #666;
}
.thumb-reveal-options > .option,
.thumb-reveal-info > .option {
    display: block;
    float: right;
    width: 26px;
    height: 26px;
    line-height: 26px;
    border-radius: 26px;
    margin-left: 5px;
    font-size: 13px;
    background: #000;
    color: #fff;
    text-align: center;
    opacity: 0.7;
    cursor: pointer;
}
.tests-manage-test-list {
    max-height: 450px;
    min-height: 450px;
    overflow-y: auto;
}
.slides-manage .list-group {
    margin-bottom: 0;
}
CSS;
$this->registerCss($css);
\backend\assets\WikidsRevealAsset::register($this);
/** @var $buttonTitle string */
?>
<button class="btn btn-primary btn-sm" type="button" id="select-slides"><?= Html::encode($buttonTitle) ?></button>
<div class="modal fade" id="select-slides-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Выбрать слайды</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= SelectStoryWidget::widget([
                            'id' => 'select-story-slides',
                            'onChange' => 'onStoryChange',
                            'storyModel' => $stories,
                        ]) ?>
                    </div>
                </div>
                <div class="slides-manage">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Слайды истории:</h4>
                            <div class="list-group tests-manage-test-list" id="all-story-slides">
                                <p class="empty-list">Введите название истории</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Выбранные слайды:</h4>
                            <div class="list-group tests-manage-test-list" id="selected-slides">
                                <p class="empty-list">Слайды не выбраны</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-selected-slides">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<< JS
var widgetConfig = window.selectStorySlidesConfig;
function makeReveal(elem) {
    var deck = new Reveal(elem, {
        embedded: true
    });
    deck.initialize({
        'width': 1280,
        'height': 720,
        'margin': 0.01,
        'transition': 'none',
        'disableLayout': true,
        'controls': false,
        'progress': false,
        'slideNumber': false
    });
    return deck;
}
function onStoryChange(storyID) {
    if (!storyID) {
        return;
    }
    $.getJSON('/admin/index.php', {
        'r': widgetConfig.slidesAction,
        'story_id': storyID
    })
        .done(function(data) {
            var list = $('#all-story-slides');
            list.empty();
            var item;
            var decks = [];
            var haveSlides = false;
            data.forEach(function(slide, i) {
                item = createListItem(slide, 'append');
                list.append(item);
                decks[i] = makeReveal(item.find('.reveal')[0]);
                haveSlides = true;
            });
            if (haveSlides) {
                $('#use-current-story').show();
            }
            else {
                $('#use-current-story').hide();
            }
        });
}
function createListItem(slideItem, action) {
    function createActionElement(slideID, className, iconClassName) {
        return $('<div/>', {
            'class': 'option ' + className,
            'data-slide-id': slideID,
            'html': '<i class="glyphicon ' + iconClassName + '"></i>'
        });
    }
    var createAppendSlideElement = function(slideID) {
        return createActionElement(slideID, 'append-slide', 'glyphicon-plus');
    }
    var createDeleteSlideElement = function(slideID) {
        return createActionElement(slideID, 'delete-slide', 'glyphicon-minus');
    }
    var actions = {
        'append': createAppendSlideElement,
        'delete': createDeleteSlideElement
    };
    return $('<div/>', {'class': 'thumb-reveal-wrapper'})
                .append(
                    $('<div/>', {'class': 'thumb-reveal-inner'})
                        .append(
                            $('<div/>', {
                                'class': 'thumb-reveal reveal',
                                'css': {'width': '1280px', 'height': '720px', 'transform': 'scale(0.28)'}
                            })
                                .append(
                                    $('<div/>', {'class': 'slides'})
                                        .append(slideItem.data)
                                )
                        )
                )
                .append(
                    $('<div/>', {'class': 'thumb-reveal-options'})
                        .append(actions[action](slideItem.id))
                )
                .append(
                    $('<div/>', {'class': 'thumb-reveal-info'})
                        .append($('<div/>', {
                            'class': 'option slide-number',
                            'text': slideItem.slideNumber,
                            'title': slideItem.story
                        }))
                );
}
(function() {
    "use strict";
    
    var modal = $('#select-slides-modal');
    $('#select-slides').on('click', function() {
        modal.modal('show');
    });
    
    var allSlidesList = $('#all-story-slides');
    var selectedSlidesList = $('#selected-slides');    
    
    var decks = [];
    modal.on('show.bs.modal', function() {
        
        selectedSlidesList.find('div.reveal').each(function(i, elem) {
            decks[i] = makeReveal(elem);
        });
        
        $('#save-selected-slides').on('click', function() {
            var selected = [];
            $('#selected-slides').find('section[data-id]').each(function() {
                selected.push($(this).data('id'));
            });
            widgetConfig.onSave(selected, modal, this);
        });
    });
    
    if (widgetConfig.selectedSlides.length > 0) {
        selectedSlidesList.empty();
    }
    widgetConfig.selectedSlides.forEach(function(slideItem) {
        var item = createListItem(slideItem, 'delete');
        selectedSlidesList.append(item);
    });
    
    selectedSlidesList.on('click', '.delete-slide', function() {
        $(this).parents('.thumb-reveal-wrapper').remove();
        if (!selectedSlidesList.find('.thumb-reveal-wrapper').length) {
            selectedSlidesList.find('p.empty-list').show();
        }
    });
    
    allSlidesList.on('click', '.append-slide', function() {
        
        var slideID = $(this).data('slideId');
        if (selectedSlidesList.find('section[data-id=' + slideID + ']').length) {
            return;
        }
        
        selectedSlidesList.find('p.empty-list').hide();
        var elem = $(this).parents('.thumb-reveal-wrapper').clone(true);
        elem
            .find('.append-slide')
            .removeClass('append-slide')
            .addClass('delete-slide')
            .end()
            .find('i.glyphicon-plus')
            .removeClass('glyphicon-plus')
            .addClass('glyphicon-minus')
            .end()
            .appendTo(selectedSlidesList);
    });
})();
JS;
$this->registerJs($js);