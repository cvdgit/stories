
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
