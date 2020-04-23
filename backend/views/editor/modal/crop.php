<?php
use yii\helpers\Html;
?>
<div class="modal fade" id="image-crop-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content loader-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Обрезать озображение</h4>
            </div>
            <div class="modal-body">
                <div id="crop-image-container"></div>
            </div>
            <div class="modal-footer">
                <?php echo Html::button('Сохранить', ['class' => 'btn btn-success', 'onclick' => 'ImageCropper.save()']) ?>
                <?php echo Html::button('Обрезать и сохранить', ['class' => 'btn btn-success', 'onclick' => 'ImageCropper.crop()']) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
