<?php

use yii\helpers\Url;

/** @var $model common\models\Story */
$action = Url::to(['story/delete-audio-file', 'id' => $model->id]);
$js = <<< CODE
$("#audio-file-list").on("click", "[data-audio-file]", function() {
    var elem = $(this);
    var fileName = elem.attr("data-audio-file");
    if (!confirm("Удалить файл озвучки " + fileName + " ?")) {
        return;
    }
    var promise = $.ajax({
        url: "$action&file=" + fileName,
        type: "GET",
        dataType: "json"
    });
    promise.done(function(data) {
        if (data && data.success) {
            toastr.success("Файл озвучки удален");
            elem.parent().remove();
        }
        else {
            toastr.error("Ошибка при удалении файла");
        }
    });
});
CODE;
/** @var $this yii\web\View */
$this->registerJs($js);

/** @var $audioUploadForm backend\models\AudioUploadForm */
?>
<ul class="list-group" id="audio-file-list">
    <?php foreach ($audioUploadForm->audioFileList() as $file): ?>
        <li class="list-group-item"><span data-audio-file="<?= $file ?>" class="badge" style="cursor: pointer">Удалить</span><?= $file ?></li>
    <?php endforeach ?>
</ul>
