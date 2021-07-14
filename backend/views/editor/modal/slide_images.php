<?php
use backend\widgets\SelectStoryWidget;
/** @var $storyModel common\models\Story */
?>
    <div class="modal fade" id="story-images-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Менеджер изображений</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-md-6 col-md-offset-3">
                        <?= SelectStoryWidget::widget([
                            'storyModel' => $storyModel,
                            'onChange' => 'changeStoryImages',
                            'id' => 'select-story-images',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <button class="btn" id="reload-story-images"><i class="glyphicon glyphicon-refresh"></i></button>
                    </div>
                </div>
                <div class="story-images-list row row-no-gutters"></div>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS

function changeStoryImages() {
    
    function addEventListeners(list) {
        list
            .off('click')
            .on('click', '[data-image-id]', function() {
                var imageID = $(this).data('imageId');
                StoryEditor.addImage(imageID).always(function() {
                    $('#story-images-modal').modal('hide');
                });
            })
            .on('click', '.delete-image', function(e) {
                e.stopPropagation();
                var imageID = $(this).parents('[data-image-id]').data('imageId');
                StoryEditor.deleteImage(imageID).done(function(response) {
                    
                });
            });
    }
    
    function createLoader() {
        return $('<div/>', {'class': 'story-images-loader'})
            .append(
                $('<div/>', {'class': 'story-images-loader-inner'})
                    .append(
                        $('<img/>', {'src': '/img/loading.gif'})
                    )
            );
    }
    function createNoImages() {
        return $('<div/>', {'class': 'no-images'}).append(
            $('<h4/>').text('Изображения в истории не найдены')
        );
    }    
    
    var list = $('.story-images-list', '#story-images-modal');
    list.empty();
    createLoader().appendTo(list);
    
    var selectize = $('#select-story-images').data('selectize'),
        storyID = selectize.getValue();
    
    StoryEditor.loadStoryImages(storyID)
        .done(function(data) {
            list.empty();
            if (data && data.success) {
                if (data.result.length === 0) {
                    list.append(createNoImages());
                    return;
                }
                data.result.forEach(function(image) {
                    $('<div/>', {'class': 'col-md-2'})
                        .append(
                            $('<div/>', {'class': 'thumbnail', 'data-image-id': image.id, 'style': 'background-image: url("' + image.thumb_url + '");'})
                                .append($('<div/>', {'class': 'thumbnail-inner'})
                                    //.append(
                                    //    $('<span/>', {'class': 'glyphicon glyphicon-trash delete-image'})
                                    //)
                                )
                                .append(
                                    $('<span/>', {'class': 'in-story', 'title': image.tooltip, 'html': '<i class="glyphicon glyphicon-ok"></i>'})
                                        .addClass(function() { return parseInt(image.deleted) === 1 ? 'hide' : ''; })
                                )
                        )
                        .appendTo(list);
                });
            }
        });
    
    addEventListeners(list);
}

(function() {
    
    $('#story-images-modal').on('show.bs.modal', function() {
        changeStoryImages();
    });

    $('#reload-story-images').on('click', function() {
        var selectize = $('#select-story-images').data('selectize'),
            storyID = selectize.getValue();
        StoryEditor.reloadStoryImage(storyID).done(function(response) {
            if (response && response.success) {
                changeStoryImages();
            }
        });
    });
})();
JS;
$this->registerJs($js);
