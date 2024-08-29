<?php

declare(strict_types=1);

use backend\assets\document_editor\DocumentEditorAsset;
use yii\web\View;

/**
 * @var View $this
 */

$this->registerCss(<<<CSS
#import-from-text-modal .main {
    min-height: 500px;
    max-height: 500px;
}
#import-from-text-modal .content {
    position: relative;
}
CSS
);

DocumentEditorAsset::register($this);

$this->registerJs($this->renderFile('@backend/views/editor/modal/_import_from_text.js'));
?>
<div class="modal fade" id="import-from-text-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Импорт слайдов из текста</h4>
            </div>
            <div class="modal-body" style="color: #000; background: #eee; margin: 0">
                <!--div class="main">
                    <div class="editor">
                        <div contenteditable="plaintext-only" class="slide-container content" style="cursor: text"></div>
                    </div>
                </div-->
                <div class="main">
                    <div></div>
                    <div class="editor" style="position: relative;height: 500px;overflow-y: auto;">
                        <div></div>
                        <div class="content" contenteditable="plaintext-only"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="import-from-text" type="button" class="btn btn-primary">Импортировать</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
