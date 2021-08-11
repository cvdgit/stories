<?php
use backend\widgets\SelectStoryWidget;
/** @var $storyModel common\models\Story */
\backend\assets\DmFileUploaderAsset::register($this);
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
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <?= SelectStoryWidget::widget([
                            'storyModel' => $storyModel,
                            'onChange' => 'changeStoryImages',
                            'id' => 'select-story-images',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <div class="pull-right">
                            <button class="btn" id="reload-story-images"><i class="glyphicon glyphicon-refresh"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row row-no-gutters">
                    <div class="col-md-2">
                        <div id="upload-images" class="dm-uploader">
                            <div class="btn btn-primary btn-block">
                                <span>Загрузить</span>
                                <input type="file" title='Загрузить изображения' />
                            </div>
                        </div>
                        <div id="files"></div>
                    </div>
                    <div class="col-md-10">
                        <div class="story-images-list row row-no-gutters"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="files-template">
    <div class="media">
        <p class="mb-2">
            <strong>%%filename%%</strong> - Status: <span class="text-muted">Waiting</span>
        </p>
        <div class="progress mb-2">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
        </div>
        <hr class="mt-1 mb-1" />
    </div>
</script>
<?php
$js = <<< JS

function changeStoryImages(replaceBlockID) {
    
    function addEventListeners(list) {
        list
            .off('click')
            .on('click', '[data-image-id]', function() {
                var imageID = $(this).data('imageId');
                if (replaceBlockID) {
                    StoryEditor.replaceImage(imageID, replaceBlockID)
                        .done(function(response) {
                            StoryEditor.replaceBlockImage(replaceBlockID, {
                                id: imageID,
                                url: response.image_path,
                                width: response.width,
                                height: response.height,
                                natural_width: response.natural_width,
                                natural_height: response.natural_height
                            })
                        })
                        .always(function() {
                            $('#story-images-modal').modal('hide');
                        });
                }
                else {
                    StoryEditor.addImage(imageID).always(function() {
                        $('#story-images-modal').modal('hide');
                    });
                }
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
    
    var replaceBlockID;
    
    function getCurrentStoryID() {
        return $('#select-story-images').data('selectize').getValue();
    }
    
    $('#story-images-modal')
        .on('show.bs.modal', function() {
            replaceBlockID = $(this).data('blockId');
            changeStoryImages(replaceBlockID);
        })
        .on('hide.bs.modal', function() {
            $(this).removeData('blockId');
        });

    $('#reload-story-images').on('click', function() {
        StoryEditor.reloadStoryImage(getCurrentStoryID()).done(function(response) {
            if (response && response.success) {
                changeStoryImages(replaceBlockID);
            }
        });
    });
    
    function ui_multi_add_file(id, file) {
        var template = $('#files-template').text();
        template = template.replace('%%filename%%', file.name);
        template = $(template);
        template.prop('id', 'uploaderFile' + id);
        template.data('file-id', id);
        $('#files').find('div.empty').fadeOut();
        $('#files').prepend(template);
    }

    function ui_multi_update_file_status(id, status, message) {
        $('#uploaderFile' + id).find('span').html(message).prop('class', 'status text-' + status);
    }

    function ui_multi_update_file_progress(id, percent, color, active) {
        color = (typeof color === 'undefined' ? false : color);
        active = (typeof active === 'undefined' ? true : active);
        var bar = $('#uploaderFile' + id).find('div.progress-bar');
        bar.width(percent + '%').attr('aria-valuenow', percent);
        bar.toggleClass('progress-bar-striped progress-bar-animated', active);
        if (percent === 0) {
            bar.html('');
        }
        else {
            bar.html(percent + '%');
        }
        if (color !== false){
            bar.removeClass('bg-success bg-info bg-warning bg-danger');
            bar.addClass('bg-' + color);
        }
    }
    
    $('#upload-images').dmUploader({
        url: '/admin/index.php?r=editor/image/upload-images',
        fieldName: "ImageForm[image]",
        extraData: function() {
            return {
                "ImageForm[story_id]": getCurrentStoryID()
            };
        },
        headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        onNewFile: function(id, file) {
            ui_multi_add_file(id, file);
        },
        onBeforeUpload: function(id) {
            ui_multi_update_file_progress(id, 0, '', true);
            ui_multi_update_file_status(id, 'uploading', 'Uploading...');
        },
        onUploadProgress: function(id, percent) {
            ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function(id, data) {
            ui_multi_update_file_status(id, 'success', 'Upload Complete');
            ui_multi_update_file_progress(id, 100, 'success', false);
            $('#uploaderFile' + id).fadeOut().remove();
        },
        onUploadError: function(id, xhr, status, message) {
            ui_multi_update_file_status(id, 'danger', message);
            ui_multi_update_file_progress(id, 0, 'danger', false);  
        },
        onComplete: function() {
            changeStoryImages();
        }
    });
})();
JS;
$this->registerJs($js);
