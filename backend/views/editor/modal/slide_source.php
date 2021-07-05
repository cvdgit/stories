<?php
use backend\assets\codemirror\CodemirrorAsset;
$css = <<< CSS
.CodeMirror {
  border: 1px solid #eee;
  font-size: 1.5rem;
  height: auto;
}
CSS;
$this->registerCss($css);
CodemirrorAsset::register($this);
?>
<div class="modal fade" id="slide-source-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content loader-lg">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <?= \conquer\codemirror\CodemirrorWidget::widget([
                    'name' => 'slideSource',
                    'presetsDir' => Yii::getAlias('@backend/components/codemirror'),
                    'preset' => 'html',
                    'settings' => [
                        'tabMode' => 'indent',
                        'styleActiveLine' => true,
                        'lineNumbers' => true,
                        //'lineWrapping' => true,
                        'autoCloseTags' => true,
                        'foldGutter' => true,
                        'lint' => true,
                    ],
                    'options' => ['rows' => 50, 'id' => 'slide-source-area'],
                ]) ?>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
$('#slide-source-modal').on('shown.bs.modal', function() {
    var editor = $('.CodeMirror')[0].CodeMirror;
    editor.setValue(StoryEditor.getNormalizedSlideContent());
    var totalLines = editor.lineCount();
    var totalChars = editor.getTextArea().value.length;
    editor.autoFormatRange({line:0, ch:0}, {line:totalLines, ch:totalChars});
    editor.setCursor(0, 0);
    editor.refresh();
});
JS;
$this->registerJs($js);
