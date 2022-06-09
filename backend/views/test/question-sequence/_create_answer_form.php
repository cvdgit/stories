<?php
use backend\assets\DmFileUploaderAsset;
use backend\assets\SortableJsAsset;
use backend\models\question\sequence\SequenceAnswerForm;
use backend\models\question\sequence\UpdateSequenceQuestion;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
/** @var View $this */
/** @var SequenceAnswerForm $model */
/** @var UpdateSequenceQuestion $questionUpdateModel */
SortableJsAsset::register($this);
DmFileUploaderAsset::register($this);
$css = <<<CSS
.no-image {
    background-color: rgba(34,34,34, .7);
}
.dm-uploader {
    cursor: pointer;
    position: relative;
}
.dm-uploader input[type=file] {
    cursor: pointer;
}
.dm-uploader .btn {
    padding: 0;
    border: 0 none;
    cursor: pointer;
    width: 100%;
    height: 100%;
    border-radius: 0;
}
#answers .media {
    border-top: 1px solid #ddd;
}
#answers .media:nth-of-type(odd) {
	background-color: #f9f9f9;
}
#answers .file-loading {
    position: absolute;
    display: none;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-image: url("/img/loading.gif");
    background-size: 50%;
    background-color: #222;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    opacity: 0.9;
    z-index: 999;
}
.ghost {
    opacity: .5;
    background: #C8EBFB;
}
.handle {
    cursor: grab;
    margin-right: 6px;
}
CSS;
$this->registerCss($css);
?>
<div style="margin-bottom:40px">
    <?php $form = ActiveForm::begin([
        'action' => ['test/question-sequence/create-answer', 'question_id' => $questionUpdateModel->getModel()->id],
        'options' => [
            'id' => 'create-answers-form',
            'style' => 'margin-bottom: 10px',
        ],
    ]) ?>
    <?= $form->field($model, 'name')->textInput(['autocomplete' => 'off'])->error(false) ?>
    <?= Html::button('Добавить', [
        'class' => 'btn btn-sm btn-primary',
        'id' => 'createAnswer',
    ]) ?>
    <?= Html::button('Добавить, разбив на слова', [
        'class' => 'btn btn-sm btn-primary',
        'id' => 'createAnswerWords',
    ]) ?>
    <?php ActiveForm::end() ?>
</div>
<div>
    <h4 style="margin-bottom: 20px">Список ответов</h4>
    <div id="answers">
        <?php foreach($questionUpdateModel->answers as $index => $answerModel): ?>
            <?= $this->render('_answer_item', ['answerModel' => $answerModel]) ?>
        <?php endforeach ?>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    'use strict';

    var el = document.getElementById('answers');
    Sortable.create(el, {
        ghostClass: 'ghost',
        handle: '.handle',
        onUpdate: function() {
            var ids = [];
            $('#answers').find('div[data-answer-id]').each(function(i, elem) {
                ids.push($(elem).data('answerId'));
            });
            $('#updatesequencequestion-sortable').val(ids.join(','));
        }
    });

    function initDmUploader(element) {
        var id = element.data('answerId');
        element.find('.dm-uploader').dmUploader({
            url: '/admin/index.php?r=test/question-sequence/image',
            multiple: false,
            fieldName: "AnswerImageUploadForm[answerImage]",
            extraData: {
                "AnswerImageUploadForm[answer_id]": id
            },
            onBeforeUpload: function(id) {
                element.find('.file-loading').show();
            },
            onUploadSuccess: function(id, data) {
                if (data && data.success) {
                    element.find('img').attr('src', data.image_path);
                    element.find('.file-loading').hide();
                }
            },
            onUploadError: function(id, xhr, status, message) {
                //ui_multi_update_file_status(id, 'danger', message);
                //ui_multi_update_file_progress(id, 0, 'danger', false);
            }
        });
    }

    $('#answers [data-answer-id]').each(function() {
        var element = $(this);
        initDmUploader(element);
    });

    $('#answers').on('click', '.delete-answer', function(e) {
        e.preventDefault();
        if (!confirm('Удалить этот ответ?')) {
            return;
        }
        var element = $(this).parents('div[data-answer-id]:eq(0)');
        var answerID = element.data('answerId');
        $.get('/admin/index.php?r=test/answer-sequence/delete', {'id': answerID})
            .done(function(response) {
                if (response && response.success) {
                    element.fadeOut().remove();
                    toastr.success('Ответ успешно удален');
                }
            });
    });

    $('#createAnswer').on('click', function() {
        $('#create-answers-form')
            .data('answer', 'full')
            .submit();
    });

    $('#createAnswerWords').on('click', function() {
        $('#create-answers-form')
            .data('answer', 'words')
            .submit();
    });

    $('#create-answers-form')
        .on('beforeSubmit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(this);

            formData.append('SequenceAnswerForm[type]', form.data('answer'));

            var button = form.find('button[type=button]');
            button.button("loading");
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(response) {
                if (response && response.success) {

                    form[0].reset();
                    toastr.success('Успешно');

                    response.answers.forEach(function(answer) {
                        $('#answers').append(answer.html);
                        initDmUploader($('#answers').find('div[data-answer-id=' + answer.answer_id + ']'));
                    });
                }
                else {
                    toastr.error(response.errors);
                }
            })
            .always(function() {
                button.button('reset');
            });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS;
$this->registerJs($js);
