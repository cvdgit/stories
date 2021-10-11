<?php
use backend\models\test_template\CreateTestsForm;
use backend\widgets\SelectStoryWidget;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\test_template\CreateTestsForm */
/** @var $items backend\models\test_template\TestItemForm[] */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Тесты</h4>
</div>
<?php $form = ActiveForm::begin(['id' => 'create-tests-form']); ?>
<div class="modal-body">
    <div>
        <?= $form->field($model, 'story_id')->widget(SelectStoryWidget::class) ?>
        <div class="row">
            <div class="col-md-3" style="padding-top: 30px">
                <?= $form->field($model, 'new_story')->checkbox() ?>
            </div>
            <div class="col-md-9">
                <?= $form->field($model, 'story_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div id="field-item-list">
            <?php foreach ($items as $index => $item): ?>
                <div class="row fields-row">
                    <div class="col-md-7">
                        <?= $form->field($item, "[$index]template_id")->dropDownList(CreateTestsForm::getTestTemplateList(), ['prompt' => '']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($item, "[$index]word_list_processing")->dropDownList(CreateTestsForm::getProcessingList(), ['prompt' => '']) ?>
                    </div>
                    <div class="col-md-1" style="display: inline-block; margin-top: 34px">
                        <a href="#" class="delete-fields-row"><i class="glyphicon glyphicon-trash"></i></a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm" id="add-fields-row">Добавить</button>
        </div>
    </div>
    <?= $form->field($model, 'word_list_id')->hiddenInput()->label(false) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Создать тесты и историю', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<<JS
(function() {
    var form = $('#create-tests-form');
    yiiModalFormInit(form, 
        function(response) {
            if (response && response.success) {
                $('#create-from-template-modal').modal('hide');
                toastr.success('История создана успешно');
            }
            else {
                toastr.error(response['error'] || 'Неизвестная ошибка');
            }
        },
        function(response) {
            toastr.error(response.responseJSON.message);
        }
    );
    
    function newFieldName(name, index) {
        var regexName = /(^.+?)([\[\d{1,}\]]{1,})(\[.+\]$)/i;
        var matches = name.match(regexName);
        if (matches && matches.length === 4) {
            matches[2] = matches[2].replace(/\]\[/g, "-").replace(/\]|\[/g, '');
            var identifiers = matches[2].split('-');
            identifiers[0] = index;
            return matches[1] + '[' + identifiers.join('][') + ']' + matches[3];
        }
    }

    function newFieldId(id, index) {
        var regexID = /^(.+?)([-\d-]{1,})(.+)$/i;
        var matches = id.match(regexID);
        if (matches && matches.length === 4) {
            matches[2] = matches[2].substring(1, matches[2].length - 1);
            var identifiers = matches[2].split('-');
            identifiers[0] = index;
            return matches[1] + '-' + identifiers.join('-') + '-' + matches[3];
        }
    }
    
    $('#add-fields-row').on('click', function() {
        
        var cloneRow = $('#field-item-list').find('.fields-row:eq(0)').clone();
        cloneRow.find('select').val('');
        cloneRow.find('.has-success').removeClass('has-success');
        cloneRow.find('.has-error').removeClass('has-error');
        
        var index = $('#field-item-list').find('.fields-row').length;
        
        cloneRow.find('select').each(function() {
            
            var name = $(this).attr('name');
            var newName = newFieldName(name, index);
            if (newName) {
                $(this).attr('name', newName);
            }
            
            var id = $(this).attr('id');
            var newId = newFieldId(id, index);
            if (newId) {
                $(this).attr('id', newId);
            }
        });
        
        $('#field-item-list').append(cloneRow);
    });
    
    $('#field-item-list').on('click', '.delete-fields-row', function(e) {
        e.preventDefault();
        
        if ($('#field-item-list').find('.fields-row').length === 1) {
            return;
        }
        
        $(this).parent().parent().remove();
        
        $('#field-item-list').find('.fields-row').each(function(index) {

            $(this).find('select').each(function() {
                
                var name = $(this).attr('name');
                var newName = newFieldName(name, index);
                if (newName) {
                    $(this).attr('name', newName);
                }
                
                var id = $(this).attr('id');
                var newId = newFieldId(id, index);
                if (newId) {
                    $(this).attr('id', newId);
                }
            });
        });
    });
})();
JS;
$this->registerJs($js);