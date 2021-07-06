<?php
use backend\models\editor\SlideLinkForm;
use backend\widgets\SelectStoryWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $storyModel common\models\Story */
$model = new SlideLinkForm();
?>
<div class="modal fade" id="slide-link-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Ссылка на слайд</h4>
            </div>
            <?php $form = ActiveForm::begin([
                'action' => ['editor/create-slide-link'],
                'id' => 'slide-link-form',
            ]) ?>
            <div class="modal-body">
                <?= $form->field($model, 'link_story_id')->widget(SelectStoryWidget::class, [
                    'storyModel' => $storyModel,
                    'linkedSlidesId' => Html::getInputId($model, 'link_slide_id'),
                ]) ?>
                <?= $form->field($model, 'link_slide_id')->dropDownList([]) ?>
                <?= $form->field($model, 'story_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Создать ссылку на слайд', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php
$js = <<< JS
(function() {
    $('#slide-link-modal').on('show.bs.modal', function() {
        $('#slide-link-form')[0].reset();
        $('#slidelinkform-link_story_id')[0].selectize.trigger('change', $('#slidelinkform-link_story_id').val());
    });
    $('#slide-link-form')
        .on('beforeSubmit', function(e) {
            e.preventDefault();
            $('#slidelinkform-story_id').val(StoryEditor.getStoryID());
            $('#slidelinkform-slide_id').val(StoryEditor.getCurrentSlideID());
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
                    toastr.success('Ссылка на слайд создана успешно');
                    StoryEditor.loadSlides(response.id);
                }
                else {
                    toastr.error(response.errors);
                }
            })
            .fail(function(response) {
                toastr.error(response.responseJSON.message);
            })
            .always(function() {
                $('#slide-link-modal').modal('hide');
            });
            return false;
        })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS;
$this->registerJs($js);
