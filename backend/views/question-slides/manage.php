<?php
use backend\widgets\SelectStoryWidget;
/** @var $questionModel common\models\StoryTestQuestion */
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
    height: 166px;
    width: 330px;
    margin-bottom: 10px;
}
.thumb-reveal-inner {
    width: 100%;
    overflow: hidden;
    border: 1px solid #eee;
    height: 166px;
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
.thumb-reveal-wrapper:hover .thumb-reveal-options {
    visibility: visible;
}
.thumb-reveal-wrapper:hover .thumb-reveal-inner {
    border-color: #666;
}
.thumb-reveal-options > .option {
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
    max-height: 600px;
    overflow-y: auto;
}
CSS;
$this->registerCss($css);
\backend\assets\OnlyRevealAsset::register($this);
\backend\assets\OnlyWikidsRevealAsset::register($this);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Тесты</h4>
</div>
<div class="modal-body">
    <div class="tests-manage">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= SelectStoryWidget::widget([
                        'id' => 'select-story-slides',
                        'onChange' => 'onStoryChange',
                    ]) ?>
                </div>
                <h4>Слайды истории:</h4>
                <div class="list-group tests-manage-test-list" id="all-story-slides"></div>
            </div>
            <div class="col-md-6" style="margin-top: 49px">
                <h4>Выбранные слайды:</h4>
                <div class="list-group tests-manage-test-list" id="selected-slides">
                    <?php foreach ($questionModel->getModifiedSlides() as $slide): ?>
                        <div class="thumb-reveal-wrapper">
                            <div class="thumb-reveal-inner">
                                <div class="thumb-reveal reveal"
                                     style="width: 1280px; height: 720px; transform: scale(0.22)">
                                    <div class="slides">
                                        <?= $slide['data'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="thumb-reveal-options">
                                <div class="option delete-slide" data-slide-id="<?= $slide['id'] ?>"><i class="glyphicon glyphicon-minus"></i></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" id="save-selected-slides">Сохранить</button>
    <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php
$questionID = $questionModel->id;
$js = <<<JS
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
        'r': 'question-slides/slides',
        'story_id': storyID
    })
        .done(function(data) {
            var list = $('#all-story-slides');
            list.empty();
            var item;
            var decks = [];
            data.forEach(function(slide, i) {
                
                item = $('<div/>', {'class': 'thumb-reveal-wrapper'})
                    .append(
                        $('<div/>', {'class': 'thumb-reveal-inner'})
                            .append(
                                $('<div/>', {
                                    'class': 'thumb-reveal reveal',
                                    'css': {'width': '1280px', 'height': '720px', 'transform': 'scale(0.22)'}
                                })
                                    .append(
                                        $('<div/>', {'class': 'slides'})
                                            .append(slide.data)
                                    )
                            )
                    )
                    .append(
                        $('<div/>', {'class': 'thumb-reveal-options'})
                            .append($('<div/>', {
                                'class': 'option append-slide',
                                'data-slide-id': slide.id,
                                'html': '<i class="glyphicon glyphicon-plus"></i>'
                            }))
                    );
                list.append(item);
                
                decks[i] = makeReveal(item.find('.reveal')[0]);
            });
        });
}

(function() {
    
    var allSlidesList = $('#all-story-slides');
    var selectedSlidesList = $('#selected-slides');    
    
    var decks = [];
    selectedSlidesList.find('div.reveal').each(function(i, elem) {
        decks[i] = makeReveal(elem);
    });
    
    selectedSlidesList.on('click', '.delete-slide', function() {
        var slideID = $(this).data('slideId');
        if (allSlidesList.find('section[data-id=' + slideID + ']').length) {
            $(this).parents('.thumb-reveal-wrapper').remove();
        }
        else {
            $(this).parents('.thumb-reveal-wrapper')
                .find('.delete-slide')
                .removeClass('delete-slide')
                .addClass('append-slide')
                .end()
                .find('i.glyphicon-minus')
                .removeClass('glyphicon-minus')
                .addClass('glyphicon-plus')
                .end()
                .appendTo(allSlidesList);
        }
    });
    
    allSlidesList.on('click', '.append-slide', function() {
        $(this).parents('.thumb-reveal-wrapper')
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
    
    $('#save-selected-slides').on('click', function() {
        
        var formData = new FormData();
        formData.append('QuestionSlidesForm[question_id]', $questionID);
        selectedSlidesList.find('section[data-id]').each(function() {
            formData.append('QuestionSlidesForm[slide_ids][]', $(this).data('id'));
        });
        var button = $(this);
        button.button("loading");
        $.ajax({
            url: '/admin/index.php?r=question-slides/create',
            type: 'POST',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(response) {
            if (response && response.success) {
                toastr.success('Изменения успешно сохранены');
                window.createQuestionSlideList(response.slides);
            }
        })
        .fail(function(response) {
            toastr.error(response.responseJSON.type, response.responseJSON.message);
        })
        .always(function() {
            button.button('reset');
            $('#manage-question-slides-modal').modal('hide');
        });
    });
})();
JS;
$this->registerJs($js);
