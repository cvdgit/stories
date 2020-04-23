<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $imageModel backend\models\editor\ImageForm */
?>
<div class="modal fade" id="image-from-file-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Добавить изображение на слайд из файл</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="text-center" style="padding: 50px 0">
                            <?php $form = ActiveForm::begin(['id' => 'create-image-form', 'action' => ['/editor/image/upload-image'], 'enableClientScript' => false]);
                            echo $form->field($imageModel, 'image', ['inputOptions' => ['class' => 'form-control']])->label(false)->fileInput();
                            echo $form->field($imageModel, 'story_id')->label(false)->hiddenInput();
                            echo Html::submitButton('Загрузить изображение', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
                            ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
$('#create-image-form').on('submit', function() {
    var form = $(this);
    SlideImageUploader.uploadHandler(form);
    return false;
});
JS;
/* @var $this yii\web\View */
$this->registerJs($js);
