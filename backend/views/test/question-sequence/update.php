<?php
use backend\assets\SortableJsAsset;
use backend\models\question\QuestionType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\sequence\UpdateSequenceQuestion */
$this->title = 'Изменить вопрос';
$this->params['sidebarMenuItems'] = [];
SortableJsAsset::register($this);
?>
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="story-test-form">
        <div class="row">
            <div class="col-md-7">
                <?php $form = ActiveForm::begin(['id' => 'update-sequence-question-form']); ?>
                <?= $form->field($model, 'story_test_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['disabled' => true]) ?>
                <?= $form->field($model, 'answers')->hiddenInput()->label(false) ?>
                <div class="form-group">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-5">
                <form class="form-inline" style="margin-bottom: 10px">
                    <div class="form-group">
                        <label for="answerText"></label>
                        <input id="answerText" type="text" class="form-control" autocomplete="off" placeholder="Введите ответ" />
                    </div>
                    <button type="button" id="createAnswer" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-plus"></i></button>
                </form>
                <div id="answers" class="list-group"></div>
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
var el = document.getElementById('answers');
var sortable = Sortable.create(el, {
    ghostClass: 'ghost',
    handle: '.handle'
});

$('#createAnswer').on('click', function() {
    var text = $('#answerText').val();
    if (!text.length) {
        return;
    }
    Answers.createAnswer({text});
    $('#answerText').val('').focus();
});

var Answers = (function(root) {
    
    function createAnswerElement(props) {
        var move = $('<i/>', {
            class: 'glyphicon glyphicon-move handle'
        });
        var del = $('<a/>', {
            html: '<i class="glyphicon glyphicon-trash"></i>',
            href: '#',
            class: 'delete-answer pull-right'
        });
        return $('<div/>', {
            text: props.text,
            class: 'list-group-item',
            'data-id': props.id
        }).prepend(move).append(del);
    }
    
    function createAnswer(props) {
        root.append(createAnswerElement(props));
    }
    
    function asJson() {
        return root.find('div.list-group-item').map(function(index) {
            return {
                id: $(this).attr('data-id'),
                text: $(this).text(),
                order: index
            };
        }).get();
    }
    
    function init(data) {
        data = data || [];
        if (typeof data === 'string') {
            data = JSON.parse(data);
        }
        data.forEach(function(item) {
            createAnswer(item);
        });
    }
    
    function extend(a, b) {
        for (var i in b) {
            a[i] = b[i];
        }
        return a;
    }
    
    function dispatchEvent(type, args) {
        var event = document.createEvent("HTMLEvents", 1, 2);
        event.initEvent(type, true, true);
        extend(event, args);
        document.dispatchEvent(event);
    }
    
    root.on('click', '.delete-answer', function(e) {
        e.preventDefault();
        var element = $(this).parent();
        var id = element.attr('data-id');
        element.remove();
        dispatchEvent('onDeleteAnswer', {
            'id': id
        });
    });
    
    return {
        'init': init,
        'createAnswer': createAnswer,
        'asJson': asJson,
        'addEventListener': function(type, listener, useCapture) {
            if ('addEventListener' in window) {
                document.addEventListener(type, listener, useCapture);
            }
        }
    };
})($('#answers'));



var element = $('#updatesequencequestion-answers');
Answers.init(element.val());
$('#update-sequence-question-form').on('beforeSubmit', function() {
    element.val(JSON.stringify(Answers.asJson()));
    return true;
});
Answers.addEventListener('onDeleteAnswer', function(args) {
    if (args.id) {
        $.get('/admin/index.php?r=test/answer-sequence/delete', {'id': args.id})
            .done(function(response) {
                if (response && response.success) {
                    toastr.success('Ответ успешно удален');
                }
            });
    }
});

JS;
$this->registerJs($js);
