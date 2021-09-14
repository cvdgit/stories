<?php
use backend\assets\DmFileUploaderAsset;
use backend\assets\SortableJsAsset;
use backend\models\question\QuestionType;
use backend\models\question\sequence\SequenceAnswerForm;
use backend\widgets\QuestionSlidesWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\sequence\UpdateSequenceQuestion */
/* @var $testModel common\models\StoryTest */
/** @var $errorText string */
$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
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
CSS;
$this->registerCss($css);
?>
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($errorText !== ''): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
            <?= Html::encode($errorText) ?>
        </div>
    <?php endif ?>
    <div class="story-test-form">
        <div class="row">
            <div class="col-md-6">
                <?php $form = ActiveForm::begin(['id' => 'update-sequence-question-form']); ?>
                <?= $form->field($model, 'story_test_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['disabled' => true]) ?>
                <?= $form->field($model, 'sortable')->hiddenInput()->label(false) ?>
                <?= QuestionSlidesWidget::widget(['model' => $model->getModel()]) ?>
                <div class="form-group form-group-controls">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-6">
                <?php
                $createAnswerForm = ActiveForm::begin([
                    'action' => ['test/question-sequence/create-answer', 'question_id' => $model->getModel()->id],
                    'options' => ['class' => 'form-inline', 'id' => 'create-answers-form', 'style' => 'margin-bottom: 10px'],
                ]);
                $createAnswerModel = new SequenceAnswerForm();
                ?>
                <?= $createAnswerForm->field($createAnswerModel, 'name')->textInput(['autocomplete' => 'off'])->label(false)->hint(false) ?>
                <?= Html::submitButton('<i class="glyphicon glyphicon-plus"></i>', ['class' => 'btn btn-sm btn-primary', 'id' => 'createAnswer']) ?>
                <?php ActiveForm::end() ?>

                <div id="answers">
                    <?php foreach($model->answers as $index => $answerModel): ?>
                    <?= $this->render('_answer_item', ['answerModel' => $answerModel]) ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$css = <<< CSS
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
    
    $('#create-answers-form')
        .on('beforeSubmit', function(e) {
            e.preventDefault();
            var form = $(this);
            var button = $(this).find('button[type=submit]');
            button.button("loading");
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: new FormData(this), 
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(response) {
                if (response && response.success) {
                    $('#answers').append(response.html);
                    initDmUploader($('#answers').find('div[data-answer-id=' + response.answer_id + ']'));
                    form[0].reset();
                    toastr.success('Ответ успешно создан');
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