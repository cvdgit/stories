<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$form = ActiveForm::begin([
    'id' => 'block-form',
]);
/** @var $model backend\models\editor\BaseForm */
/** @var $this yii\web\View  */
?>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'align', ['inputOptions' => ['class' => 'form-control input-sm editor-align']])->dropDownList(\backend\models\editor\base\BlockAlign::asArray(), ['prompt' => 'Не выбрано']) ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'stretch', ['inputOptions' => ['class' => 'form-control input-sm editor-stretch']])->dropDownList([]) ?>
    </div>
</div>
<?= $this->render($model->view, ['form' => $form, 'model' => $model]) ?>
<div>
    <div style="text-align: center; margin: 6px; cursor: pointer; border-bottom: 1px #808080 dashed" class="show-block-params">Показать параметры</div>
    <div class="block-params hide">
        <div class="row">
            <div class="col-xs-6"><?= $form->field($model, 'width', ['inputOptions' => ['class' => 'form-control input-sm editor-width']]) ?></div>
            <div class="col-xs-6"><?= $form->field($model, 'top', ['inputOptions' => ['class' => 'form-control input-sm editor-top']]) ?></div>
        </div>
        <div class="row">
            <div class="col-xs-6"><?= $form->field($model, 'height', ['inputOptions' => ['class' => 'form-control input-sm editor-height']]) ?></div>
            <div class="col-xs-6"><?= $form->field($model, 'left', ['inputOptions' => ['class' => 'form-control input-sm editor-left']]) ?></div>
        </div>
    </div>
</div>
<?php
echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
ActiveForm::end();

$js = <<< JS
$('.editor-align').on('change', function() {
    switch (this.value) {
        case '1':
            StoryEditor.setBlockAlignLeft();
            break;
        case '2':
            StoryEditor.setBlockAlignRight();
            break;
        case '3':
            StoryEditor.setBlockAlignTop();
            break;
        case '4':
            StoryEditor.setBlockAlignBottom();
            break;
        case '5':
            StoryEditor.setBlockAlignHorizontalCenter();
            break;
        case '6':
            StoryEditor.setBlockAlignVerticalCenter();
            break;
        case '7':
            StoryEditor.setBlockAlignSlideCenter();
            break;
    }
});
JS;
$this->registerJs($js);