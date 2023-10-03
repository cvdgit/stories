<?php

declare(strict_types=1);

use backend\widgets\SelectStorySlidesWidget;
use yii\helpers\Json;

/**
 * @var $slides array
 * @var $questionID int
 */

$css = <<<CSS
.question-slides-block {
    margin: 20px 0;
}
.question-slides-block h4 {
    height: 35px;
    line-height: 35px;
}
CSS;
$this->registerCss($css);
?>
<div class="question-slides-block">
    <h4>Связанные слайды</h4>
    <table class="table table-bordered" id="question-slides-list">
        <thead>
            <tr>
                <th>№</th>
                <th>История</th>
                <th>Слайд</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3">Пусто</td>
            </tr>
        </tbody>
    </table>
    <?= SelectStorySlidesWidget::widget([
        'slidesAction' => 'story/widget/slides',
        'onSave' => 'onSaveSlides',
        'selectedSlides' => $slides,
        'buttonTitle' => 'Выбрать слайды'
    ]) ?>
</div>
<?php
$slidesJson = Json::encode($slides);
$js = <<< JS
window['createQuestionSlideList'] = function(slides) {
    var list = $('#question-slides-list tbody');
    list.empty();
    if (slides.length === 0) {
        list.append('<tr><td colspan="3">Пусто</td></tr>')
    }
    slides.forEach(function(item, i) {
        $('<tr/>')
            .append($('<td/>').text(++i))
            .append($('<td/>').text(item.story))
            .append($('<td/>').text(item.slideNumber))
            .appendTo(list);
    });
};
function onSaveSlides(selected, modal, targetElement) {

    var formData = new FormData();
    formData.append('QuestionSlidesForm[question_id]', $questionID);
    selected.forEach(function(slideID) {
        formData.append('QuestionSlidesForm[slide_ids][]', slideID);
    });

    var button = $(targetElement);
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
        modal.modal('hide');
    });


    /*var list = $('#story-slides').find('.selected-slides');
    list.empty();

    selected = selected || [];
    if (selected.length > 0) {
        $('<p/>', {'text': 'Слайды выбраны. История будет создана после сохранения.'})
            .appendTo(list);
    }

    var modelName = '';
    selected.forEach(function(slideID) {
        list.append($('<input/>', {
            'type': 'hidden',
            'name': modelName + '[slide_ids][]',
            'value': slideID
        }));
    });
    modal.modal('hide');*/
}
(function() {
    "use strict";

    var questionSlides = $slidesJson;
    createQuestionSlideList(questionSlides);
})();
JS;
$this->registerJs($js);
