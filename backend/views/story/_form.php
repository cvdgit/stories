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

$id = Html::getInputId($model, 'source_id');
$sourceDropBoxID = Html::getInputId($model, 'source_dropbox');
$sourcePowerPointID = Html::getInputId($fileUploadForm, 'storyFile');
$sourceDropBoxConst = Story::SOURCE_SLIDESCOM;
$sourcePowerPointConst = Story::SOURCE_POWERPOINT;
$sourceOnChange = <<< JS
$(function() {
  $('#$id').trigger('change');
});
$('#$id').on('change', function() {
    var sourceDropBoxElem = $('#$sourceDropBoxID'),
        sourcePowerPointElem = $('#$sourcePowerPointID');

    sourceDropBoxElem.parent().hide();
    sourcePowerPointElem.parent().hide();

    if (this.value == $sourceDropBoxConst) {
        sourceDropBoxElem.parent().show();
    }

    if (this.value == $sourcePowerPointConst) {
        sourcePowerPointElem.parent().show();
    }
});
JS;
$this->registerJs($sourceOnChange, yii\web\View::POS_READY);
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?php if (!$model->isNewRecord): ?>
<?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
<?php endif ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<?= $form->field($coverUploadForm, 'coverFile')->fileInput() ?>
<?php if (!empty($model->cover)): ?>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <a href="#" class="thumbnail"><img src="<?= $this->context->service->getCoverPath($model->cover, true) ?>" alt="..."></a>
    </div>
</div>
<?php endif ?>
<?= $form->field($model, 'source_id')->dropDownList(Story::getSourceList(), ['prompt' => 'Выбрать']) ?>
<?= $form->field($model, 'source_dropbox', ['options' => ['style' => 'display: none']])->textInput(['maxlength' => true]) ?>
<?= $form->field($fileUploadForm, 'storyFile', ['options' => ['style' => 'display: none']])->fileInput() ?>
<?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'profileName'),
                                                          ['prompt' => 'Выбрать', 'readonly' => !Yii::$app->user->can('admin')]) ?>
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
</div>
<?php ActiveForm::end(); ?>
