<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Story;
use common\models\Category;
use yii\helpers\Url;
use dosamigos\selectize\SelectizeTextInput;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $form yii\widgets\ActiveForm */

$url = Url::to(['/story/getfromdropbox', 'id' => $model->id]);
$script = <<< JS
function BootstrapAlert() {
    this.htmlBegin = '<div class="alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    this.htmlEnd = '</div>';
}
BootstrapAlert.prototype.success = function(message) {
    return $(this.htmlBegin + message + this.htmlEnd).addClass('alert-success');
}
BootstrapAlert.prototype.error = function(message) {
    return $(this.htmlBegin + message + this.htmlEnd).addClass('alert-danger');
}
var bAlert = new BootstrapAlert();
$('#dropbox-get-data').on('click', function() {
    $.get('$url')
        .done(function(data) {
            if (data) {
                var elem;
                if (data.error.length)
                    elem = bAlert.error(data.error);
                else
                    elem = bAlert.success(data.success);
                elem.appendTo('#alert_placeholder');
            }
		})
        .fail(function(data) {
            console.log('fail');
        });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?php if (!empty($model->cover)): ?>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <a href="#" class="thumbnail"><img src="<?= $this->context->service->getCoverPath($model->cover, true) ?>" alt="..."></a>
    </div>
</div>
<?php endif ?>
<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?= $form->field($coverUploadForm, 'coverFile')->fileInput() ?>
<?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'dropbox_story_filename')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'username'), ['prompt' => 'Выбрать']) ?>
<?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(Category::find()->all(), 'id', 'name'), ['prompt' => 'Выбрать']) ?>
<?= $form->field($model, 'sub_access')->checkBox() ?>
<?= $form->field($model, 'status')->dropDownList([Story::STATUS_DRAFT => 'Черновик', Story::STATUS_PUBLISHED => 'Публикация'], ['prompt' => 'Выбрать']) ?>
<?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::className(), [
    'loadUrl' => ['tag/list'],
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
        'plugins' => ['remove_button'],
        'valueField' => 'name',
        'labelField' => 'name',
        'searchField' => ['name'],
        'create' => true,
    ],
])->hint('Используйте запятые для разделения тегов') ?>
<div class="form-group">
    <?= Html::submitButton(($model->isNewRecord ? 'Создать историю' : 'Сохранить изменения'), ['class' => 'btn btn-success']) ?>
    <?php if (!$model->isNewRecord): ?>
    <?= Html::button('Получить данные из Dropbox', ['class' => 'btn btn-primary', 'id' => 'dropbox-get-data']) ?>
    <?php endif ?>
</div>
<?php ActiveForm::end(); ?>
