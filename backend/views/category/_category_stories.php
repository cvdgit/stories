<?php
/** @var $models common\models\Story[] */
use backend\assets\SortableJsAsset;
use common\components\StoryCover;
use yii\helpers\Html;
SortableJsAsset::register($this);
$css = <<<CSS
#manage-slides-list {
    min-height: 400px;
    max-height: 500px;
    overflow-y: auto;
}
#manage-slides-list .selected {
    background-color: #f9c7c8;
    border: solid red 1px !important;
    z-index: 1 !important;
}
CSS;
$this->registerCss($css);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Истории категории</h4>
</div>
<div class="modal-body">
    <?php if (empty($models)): ?>
    <p>Историй нет</p>
    <?php else: ?>
    <div id="manage-slides-list">
        <?php foreach($models as $model): ?>
        <div class="media" data-story-id="<?= $model->id ?>">
            <div class="media-left">
                <?= Html::img(StoryCover::getListThumbPath($model->cover), [
                    'class' => 'media-object',
                    'alt' => Html::encode($model->title),
                    'style' => 'width: 64px'
                ]) ?>
            </div>
            <div class="media-body">
                <h4 class="media-heading"><?= Html::encode($model->title) ?></h4>
                <p></p>
            </div>
        </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" id="save-story-order">Сохранить</button>
    <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php
$js = <<<JS
(function() {
    'use strict';

    Sortable.create($('#manage-slides-list')[0], {
        handle: '.media-left',
        multiDrag: true,
        selectedClass: 'selected'
    });
    
    $('#save-story-order').on('click', function() {
        var button = $(this);
            button.button('loading');
        var formData = new FormData();
        var order = 1;
        $('#manage-slides-list [data-story-id]').each(function() {
            formData.append('StoryEpisodeOrderForm[order][' + order + ']', $(this).data('storyId'));
            order++;
        });
        $.ajax({
            url: '/admin/index.php?r=story/save-episode-order',
            type: 'POST',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(response) {
            if (response && response.success) {
                toastr.success('Изменения успешно сохранены');
            }
            else {
                toastr.error('Ошибка');
            }
        })
        .fail(function(response) {
            toastr.error(response.responseJSON.type, response.responseJSON.message);
        })
        .always(function() {
            button.button('reset');
            $('#manage-stories-modal').modal('hide');
        });
    });
})();
JS;
$this->registerJs($js);
