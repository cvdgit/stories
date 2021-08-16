<?php
/** @var $slides string */
/** @var $remote string */
$css = <<<CSS
.question-slides-block {
    margin: 30px 0;
}
.question-slides-block h4 {
    height: 35px;
    line-height: 35px;
}
CSS;
$this->registerCss($css);
?>
<div class="question-slides-block">
    <h4>Связанные слайды <span class="pull-right"><button class="btn btn-primary" type="button" id="manage-slides">Выбрать слайды</button></span></h4>
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
</div>
<div class="modal remote fade" id="manage-question-slides-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<?php
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
            .append($('<td/>').text(item.number))
            .appendTo(list);
    });
};
(function() {
    "use strict";
    
    var questionSlides = $slides;
    createQuestionSlideList(questionSlides);
    var modal = $('#manage-question-slides-modal');
    $('#manage-slides').on('click', function() {
        modal.modal({'remote': '$remote'});
    });
    modal.on('loaded.bs.modal', function() {

    });
})();
JS;
$this->registerJs($js);
