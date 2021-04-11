<?php
use common\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model backend\models\question\QuestionModel */
/** @var $form yii\widgets\ActiveForm */
/** @var $dataProvider yii\data\ActiveDataProvider */
$isNewRecord = $model instanceof \backend\models\question\CreateQuestion;
?>
<div class="story-test-form">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'type')->dropDownList(\backend\models\question\QuestionType::asArray()) ?>
            <?= $form->field($model, 'mix_answers')->checkbox() ?>
            <?= $form->field($model, 'imageFile')->fileInput() ?>
            <?php if (!$isNewRecord && $model->haveImage()): ?>
            <div style="padding: 20px 0; text-align: center">
                <?= Html::img($model->getImageUrl(), ['style' => 'max-width: 330px']) ?>
                <div>
                    <?= Html::a('Удалить изображение', ['question/delete-image', 'id' => $model->getModelID()]) ?>
                </div>
            </div>
            <?php endif ?>
            <div class="form-group">
                <?= Html::submitButton(($isNewRecord ? 'Создать' : 'Изменить') . ' вопрос', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-6">
            <?php if (!$isNewRecord): ?>
                <div>
                    <p>
                        <?= Html::a('Создать ответ', ['test/create-answer', 'question_id' => $model->getModelID()], ['class' => 'btn btn-primary']) ?>
                    </p>
                    <h4>Ответы на вопрос</h4>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['class' => 'table-responsive'],
                        'columns' => [
                            'name',
                            'is_correct',
                            [
                                'class' => ActionColumn::class,
                                'template' => '{update} {delete}',
                                'urlCreator' => function($action, $model, $key, $index) {
                                    $url = '';
                                    if ($action === 'update') {
                                        $url = Url::to(['test/update-answer', 'answer_id' => $model->id]);
                                    }
                                    if ($action === 'delete') {
                                        $url = Url::to(['test/delete-answer', 'answer_id' => $model->id]);
                                    }
                                    return $url;
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="modal fade" id="make-answers-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ответы на вопрос</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <select name="label" id="graph-label" class="form-control" style="margin-bottom: 10px"></select>
                        <select name="node" id="graph-node" class="form-control" style="margin-bottom: 10px"></select>
                        <select name="relation" id="graph-relation" class="form-control"></select>
                    </div>
                    <div class="col-md-6">
                        <h4>Варианты ответов</h4>
                        <div id="graph-answers"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="make-answers">Сформировать ответы</button>
                <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php
$questionID = $isNewRecord ? '' : $model->getModelID();
$js = <<< JS
(function() {
    "use strict";

    var questionID = '$questionID';
    
    var modal = $("#make-answers-modal"),
        labels = $("#graph-label"),
        nodes = $("#graph-node"),
        relations = $("#graph-relation"),
        answers = $("#graph-answers");
    
    function resetSelect(select) {
        select.empty();
        $("<option/>").val("").text("").appendTo(select);
    }
    
    labels.on("change", function() {
        
        resetSelect(nodes);
        resetSelect(relations);
        answers.empty();
        
        var type = $(this).val();
        var promise = $.ajax({
            "url": "/admin/index.php?r=neo/nodes-by-type&type=" + type,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                }
                else {
                    data.result.forEach(function(node) {
                        $("<option/>")
                            .val(node)
                            .text(node)
                            .appendTo(nodes);
                    });
                }
            }
        })
        .fail(function(data) {
            var response = data.responseJSON;
            toastr.error(response.message, response.name);
        });
    });
    
    nodes.on("change", function() {
        resetSelect(relations);
        var name = $(this).val(),
            type = labels.val();
        var promise = $.ajax({
            "url": "/admin/index.php?r=neo/relationships&type=" + type + "&name=" + name,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                }
                else {
                    data.result.forEach(function(relation) {
                        $("<option/>")
                            .val(relation)
                            .text(relation)
                            .appendTo(relations);
                    });
                }
            }
        })
        .fail(function(data) {
            var response = data.responseJSON;
            toastr.error(response.message, response.name);
        });
    });
    
    relations.on("change", function() {
        answers.empty();
        var relation = $(this).val(),
            type = labels.val(),
            name = nodes.val();
        var promise = $.ajax({
            "url": "/admin/index.php?r=neo/answers&type=" + type + "&name=" + name + "&relation=" + relation,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                }
                else {
                    data.result.forEach(function(answer) {
                        $('<div class="row" style="margin-bottom: 10px"><div class="col-md-1"><input type="checkbox" checked></div><div class="col-md-11"><input class="form-control answer-text" type="text" value=""></div></div>')
                            .find(".answer-text").val(answer).end()
                            .appendTo(answers);
                    });
                }
            }
        })
        .fail(function(data) {
            var response = data.responseJSON;
            toastr.error(response.message, response.name);
        });
    });
    
    modal.on("show.bs.modal", function() {
        
        resetSelect(labels);
        resetSelect(nodes);
        resetSelect(relations);
        answers.empty();
        
        var promise = $.ajax({
            "url": "/admin/index.php?r=neo/labels",
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                }
                else {
                    data.result.forEach(function(label) {
                        $("<option/>")
                            .val(label)
                            .text(label)
                            .appendTo(labels);
                    });
                }
            }
        });
    });
    
    $("#make-answers").on("click", function() {
        var text = [];
        answers.find("div.row").each(function() {
            if ($(this).find("input[type=checkbox]").is(":checked")) {
                text.push("answers[]=" + $(this).find(".answer-text").val());
            }
        });
        if (text.length === 0) {
            return;
        }
        var promise = $.ajax({
            "url": "/admin/index.php?r=test/import-answers",
            "type": "POST",
            "data": "question_id=" + questionID + "&" + text.join("&")
        });
        promise.done(function(data) {
            if (data && data.success) {
                location.reload();
            }
        })
        .fail(function(data) {
            var response = data.responseJSON;
            toastr.error(response.message, response.name);
        });
    });
})();
JS;
$this->registerJs($js);