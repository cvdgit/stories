<?php
use backend\assets\codemirror\CodemirrorAsset;
use backend\models\editor\SlideSourceForm;
use conquer\codemirror\CodemirrorWidget;
use yii\widgets\ActiveForm;
$css = <<< CSS
.CodeMirror {
  border: 1px solid #eee;
  font-size: 1.5rem;
  height: 600px;
}
CSS;
$this->registerCss($css);
CodemirrorAsset::register($this);
$model = new SlideSourceForm();
?>
<div class="modal fade" id="slide-source-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['action' => ['editor/save-slide-source'], 'id' => 'slide-source-form']) ?>
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'source')->widget(CodemirrorWidget::class, [
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
                ])->label(false) ?>
                <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    
    var modal = $('#slide-source-modal');
    var form = $('#slide-source-form', modal);
    
    modal
        .on('show.bs.modal', function() {
            $('#slidesourceform-slide_id', this).val(StoryEditor.getCurrentSlideID());
        })
        .on('shown.bs.modal', function() {
            var editor = $('.CodeMirror')[0].CodeMirror;
            editor.setValue(StoryEditor.getNormalizedSlideContent());
            var totalLines = editor.lineCount();
            var totalChars = editor.getTextArea().value.length;
            editor.autoFormatRange({line:0, ch:0}, {line:totalLines, ch:totalChars});
            editor.setCursor(0, 0);
            editor.refresh();
        });
    
    form.on('beforeSubmit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: new FormData(this), 
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(response) {
                if (response && response.success) {
                    StoryEditor.loadSlide(response.id);
                }
                else {
                    toastr.error(response.errors);
                }
            })
            .always(function() {
                modal.modal('hide');
            });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS;
$this->registerJs($js);
