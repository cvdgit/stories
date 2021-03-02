<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model backend\models\WordListAsTextForm */
/** @var $target string */
?>
<div class="modal fade" id="story-text-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Список слов как текст</h4>
            </div>
            <?php $wordListForm = ActiveForm::begin([
                'action' => ['word-list/create-from-text'],
                'options' => [
                    'id' => 'word-list-as-text-form'
                ],
                'validateOnSubmit' => false,
            ]); ?>
            <div class="modal-body">
                <div>
                    <a href="#" id="split-text" class="btn">Разбить по предложениям</a>
                    <a href="#" id="split-text-by-word" class="btn">Разбить по словам</a>
                    <a href="#" id="missing-words" class="btn">Вставить пропуск</a>
                </div>
                <?= $wordListForm->field($model, 'text')->textarea(['cols' => 30, 'rows' => 20]) ?>
                <?= $wordListForm->field($model, 'word_list_id')->hiddenInput()->label(false) ?>
            </div>
            <div class="modal-footer">
                <div class="alert alert-danger text-left">Будет очищена история прохождения тестов, в которых указан текущий список слов</div>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js = <<< JS

var WordList = (function() {
    var element = $('#wordlistastextform-text');
    return {
        'getElement' : function() {
            return element;
        },
        'getText': function() {
            return element.val();
        },
        'setText': function(text) {
            element.val(text);
        }
    }
})();

$('$target').on('click', function(e) {
    e.preventDefault();
    $.get($(this).attr('href')).done(function(response) {
        WordList.setText(response);
        $('#story-text-modal')
            .modal('show');
    });
});

$('#word-list-as-text-form')
    .on('beforeSubmit', function(e) {
        e.preventDefault();
    })
    .on('submit', function(e) {
        e.preventDefault();
        var form_data = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: form_data, 
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(response) {
                if (response && response.success) {
                    fillTestWordsTable(response.params);
                }
                else {
                    toastr.error(response.errors);
                }
            })
        .always(function() {
            $('#story-text-modal').modal('hide');
        });
        return false;
    });

$('#split-text').on('click', function(e) {
    e.preventDefault();
    var text = WordList.getText();
    text = text.replace(/\. /g, ".\\n");
    text = text.replace(/\! /g, "!\\n");
    text = text.replace(/\? /g, "?\\n");
    WordList.setText(text);
});

$('#split-text-by-word').on('click', function(e) {
    e.preventDefault();
    var text = WordList.getText();
    text = text
        .split(' ')
        .map(function(value) {
            return value.replace(/[^\wа-яёЁ\-]+/ig, '');
        })
        .filter(function(value) {
            return value.replace(/[^\wа-яёЁ]+/ig, '') !== '';
        })
        .join("\\n");
    WordList.setText(text);
});

$('#missing-words').on('click', function(e) {
    e.preventDefault();
    var element = WordList.getElement()[0];
    var selected;
    var boundaries = {
        start: element.selectionStart,
        end: element.selectionEnd
    }
    if (element.selectionStart === element.selectionEnd) {
        if (element.selectionStart > 0) {
            var text = element.value;
            if (text) {
                var i = 0;
                var reg = /[^\wа-яёЁ\-]+/ig;
                while (i < 1) {
                    var start = boundaries.start;
                    var end = boundaries.end;
                    var prevChar = text.charAt(start - 1);
                    var currentChar = text.charAt(end);
                    if (!prevChar.match(reg) && prevChar.length > 0) {
                        boundaries.start--;
                    }
                    if (!currentChar.match(reg) && currentChar.length > 0) {
                        boundaries.end++;
                    }
                    if (start === boundaries.start && end === boundaries.end) {
                        i = 1;
                    }
                }
                selected = text.slice(boundaries.start, boundaries.end)
            }
        }
    }
    else {
        selected = element.value.slice(element.selectionStart, element.selectionEnd);
    }
    element.setRangeText('{' + selected + '}', boundaries.start, boundaries.end);
});

JS;
$this->registerJs($js);
