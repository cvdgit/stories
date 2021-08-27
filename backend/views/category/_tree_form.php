<?php
use backend\models\category\CreateTreeForm;
use yii\widgets\ActiveForm;
$model = new CreateTreeForm();
?>
<div class="modal fade" id="create-tree-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['action' => ['category/create-root'], 'options' => ['id' => 'create-tree-form']]); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Новое дерево</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'name')->textInput() ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Создать</button>
                <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    var modal = $('#create-tree-modal');
    var form = $('#create-tree-form', modal);
    modal
        .on('show.bs.modal', function() {
            form[0].reset();
        });
    form.on('beforeSubmit', function(e) {
        e.preventDefault();
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
                    location.reload();
                }
                else {
                    toastr.error(response.errors);
                }
            })
            .always(function() {
                modal.modal('hide');
            });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS;
/* @var $this yii\web\View */
$this->registerJs($js);