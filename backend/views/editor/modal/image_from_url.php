<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $imageModel backend\models\editor\ImageFromUrlForm */
?>
<div class="modal fade" id="image-from-url-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Добавить изображение на слайд с другого сайта</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="text-center" style="padding: 50px 0">
                            <?php
                            $form = ActiveForm::begin(['id' => 'create-image-from-url-form', 'action' => ['/editor/image/image-from-url'], 'enableClientScript' => false]);
                            echo $form->field($imageModel, 'url', ['inputOptions' => ['class' => 'form-control']])->textInput(['autocomplete' => 'off']);
                            echo $form->field($imageModel, 'story_id')->label(false)->hiddenInput();
                            echo Html::submitButton('Добавить изображение', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
                            ActiveForm::end();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
