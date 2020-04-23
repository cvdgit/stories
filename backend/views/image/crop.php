<?php

use yii\helpers\Html;

/** @var $this yii\web\View */
\backend\assets\CropperAsset::register($this);

$js = <<< JS
var ratio = $ratio;
console.log(ratio);
var options = {
    aspectRatio: ratio,
    viewMode: 1
};
var cropper = new Cropper(document.getElementById('crop-image'), options);
JS;
$this->registerJs($js);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Изображение</h4>
</div>
<div class="modal-body" style="height: 500px">
    <div style="max-width: 100%">
        <?= Html::img($path, ['id' => 'crop-image', 'style' => 'display: block; width: 100%', 'width' => 500]) ?>
    </div>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
