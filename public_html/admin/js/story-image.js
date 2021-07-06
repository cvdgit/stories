/** Story Images */
(function(editor, $, console) {
    "use strict";

    editor.loadStoryImages = function(storyID) {
        if (!storyID) {
            throw "loadStoryImages: storyID is null";
        }
        return $.getJSON('/admin/index.php', {
            'r': 'editor/image/get-images',
            'story_id': storyID
        });
    };

    editor.addImage = function(imageID) {
        if (!imageID) {
            throw "addImage: imageID is null";
        }
        var formData = new FormData();
        formData.append('ImageForm[slide_id]', editor.getCurrentSlideID());
        formData.append('ImageForm[image_id]', imageID);
        return $.ajax({
            url: '/admin/index.php?r=editor/create-block/image',
            type: 'POST',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(response) {
                if (response && response.success) {
                    editor.createSlideBlock(response.html);
                }
                else {
                    toastr.error(response.errors);
                }
            });
    };

    editor.deleteImage = function(imageID) {
        if (!imageID) {
            throw "deleteImage: imageID is null";
        }
        if (!confirm('Изображение будет удалено из всех историй. Продолжить?')) {
            return;
        }
    };

    /*
    var $modal = $("#story-images-modal");

    function elementWrapper() {
        return $('<div class="media">' +
                    '<div class="media-left"></div>' +
                    '<div class="media-body">' +
                        '<a href="#" class="add-backup-image">Добавить резервное изображение</a>' +
                        '<br>' +
                        '<a href="#" class="backup-images">Резервные изображения <span></span></a>' +
                        '<br>' +
                        '<a href="#" class="delete-image">Удалить из истории</a>' +
                    '</div>' +
                '</div>');
    }

    function backupImages(imageID) {
        editor.slideCollectionsBackupModal(imageID);
    }

    function deleteImageFromStory(imageID, slideID, blockID, elem) {
        return $.ajax({
            "url": "/admin/index.php?r=editor/image/delete-from-story&image_id=" + imageID + '&slide_id=' + slideID + '&block_id=' + blockID,
            "type": "GET",
            "dataType": "json"
        });
    }

    $modal.on('show.bs.modal', function() {
        var $imagesList = $(".story-images-list", this);
        $imagesList.empty();
        var promise = $.ajax({
            "url": "/admin/index.php?r=editor/image/get-images&story_id=" + editor.getConfigValue('storyID'),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                }
                else {
                    data.result.forEach(function(image) {
                        var $img = $('<img/>')
                            .attr("src", "/image/view?id=" + image.hash)
                            .attr("width", 200);
                        elementWrapper()
                            .find(".media-left").append($img).end()
                            .find(".add-backup-image").on("click", function(e) {
                                e.preventDefault();
                                backupImages(image.id);
                            }).end()
                            .find(".backup-images").attr("href", "/admin/index.php?r=editor/image/update&id=" + image.id).end()
                            .find(".backup-images span").text(" (" + image.link_image_count + ")").end()
                            .find(".delete-image").on("click", function(e) {
                                e.preventDefault();
                                if (!confirm("Удалить изображение?")) {
                                    return;
                                }
                                var $link = $(this);
                                deleteImageFromStory(image.id, image.slide_id, image.block_id)
                                    .done(function(data) {
                                        if (data && data.success) {
                                            $link.parent().parent().remove();
                                            editor.loadSlide(editor.getCurrentSlideID(), true);
                                        }
                                    });
                            }).end()
                            .appendTo($imagesList);
                    });
                }
            }
        });
    });
     */

})(StoryEditor, jQuery, console);

/**
function ImageManager() {

}

function BlockAction(selector, actionCallback) {
    'use strict';

    this.action = $(selector);
    this.action.on('click', actionCallback);
}

BlockAction.prototype.reset = function() {

};

var imageBlockAction = new BlockAction('#create-image-action', function() {

});


var EditorActions = (function() {
    "use strict";

    var $createImageAction = $('#create-image-action');

    function beginImageBlock() {
        EditorImage.init($('#slide-container'));
    }

    function endImageBlock() {
        EditorImage.destroy();
    }

    function toggleImageBlock(e) {
        var checked = !$(e.target).hasClass('active');
        if (checked) {
            beginImageBlock();
        }
        else {
            endImageBlock();
        }
    }

    $createImageAction.on('click', toggleImageBlock);

    function reset() {
        $createImageAction.hasClass('active') && $createImageAction.removeClass('active');
        endImageBlock();
    }

    return {
        'reset': reset
    };
})();
*/
