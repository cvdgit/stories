<?php
use backend\models\editor\ImageFromUrlForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $storyModel common\models\Story */
$imageModel = new ImageFromUrlForm();
$imageModel->story_id = $storyModel->id;
?>
<div class="modal fade" id="image-from-url-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Добавить изображение из ссылки</h4>
            </div>
            <div class="modal-body">
                <div class="text-center" style="padding: 25px 0">
                    <div class="file-loading">
                        <img src="/img/loading.gif" alt="loading">
                    </div>
                    <?php $form = ActiveForm::begin(['id' => 'create-image-from-url-form', 'action' => ['editor/create-block/image-from-url']]) ?>
                    <?= $form->field($imageModel, 'url', ['inputOptions' => ['class' => 'form-control']])->label(false)->textInput(['autocomplete' => 'off']) ?>
                    <?= $form->field($imageModel, 'story_id')->label(false)->hiddenInput() ?>
                    <?= $form->field($imageModel, 'slide_id')->label(false)->hiddenInput() ?>
                    <?= Html::submitButton('Добавить изображение', ['class' => 'btn btn-primary']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    var modal = $('#image-from-url-modal');
    var form = $('#create-image-from-url-form', modal);
    var fileLoading = $('.file-loading', modal);
    modal
        .on('show.bs.modal', function() {
            form[0].reset();
        })
        .on('shown.bs.modal', function() {
            $('input[type=text]:eq(0)', this).focus();
        })
        .on('hidden.bs.modal', function() {
            fileLoading.hide();
        });
    form
        .on('beforeSubmit', function(e) {
            e.preventDefault();
            fileLoading.show();
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
$this->registerJs($js);
