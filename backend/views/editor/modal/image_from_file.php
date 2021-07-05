<?php
use backend\models\editor\ImageFromFileForm;
use yii\widgets\ActiveForm;
/** @var $storyModel common\models\Story */
$imageModel = new ImageFromFileForm();
$imageModel->story_id = $storyModel->id;
?>
<div class="modal fade" id="image-from-file-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Изображение из файла</h4>
            </div>
            <div class="modal-body">
                <div style="padding: 50px 0">
                    <div class="file-loading">
                        <img src="/img/loading.gif" alt="loading">
                    </div>
                    <?php $form = ActiveForm::begin(['id' => 'image-from-file-form', 'action' => ['editor/create-block/image-from-file']]) ?>
                    <?= $form->field($imageModel, 'image', ['inputOptions' => ['class' => 'form-control']])->label(false)->fileInput() ?>
                    <?= $form->field($imageModel, 'story_id')->label(false)->hiddenInput() ?>
                    <?= $form->field($imageModel, 'slide_id')->label(false)->hiddenInput() ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    var modal = $('#image-from-file-modal');
    var form = $('#image-from-file-form', modal);
    var fileLoading = $('.file-loading', modal);
    modal
        .on('shown.bs.modal', function() {
            form[0].reset();
        })
        .on('hidden.bs.modal', function() {
            fileLoading.hide();
        });
    form.find('#imagefromfileform-image').on('change', function() {
        fileLoading.show();
        form.submit();
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
                    StoryEditor.createSlideBlock(response.html);
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
