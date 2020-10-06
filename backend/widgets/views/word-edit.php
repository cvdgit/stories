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
                <?= $wordListForm->field($model, 'text')->textarea(['cols' => 30, 'rows' => 20]) ?>
                <?= $wordListForm->field($model, 'word_list_id')->hiddenInput()->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js = <<< JS
$('$target').on('click', function(e) {
    e.preventDefault();
    $.get($(this).attr('href')).done(function(response) {
        $('#wordlistastextform-text').val(response);
        $('#story-text-modal')
            .modal('show');
    });
});

$('#word-list-as-text-form')
    .on('beforeSubmit', function(e) {
        console.log('beforeSubmit');
        e.preventDefault();
    })
    .on('submit', function(e) {
        console.log('submit');
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
JS;
$this->registerJs($js);
