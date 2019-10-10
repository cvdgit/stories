<?php

use common\components\StoryCover;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\User;
use dosamigos\selectize\SelectizeTextInput;

/** @var $this yii\web\View */
/** @var $model common\models\Story */
/** @var $form yii\widgets\ActiveForm */
/** @var $fileUploadForm backend\models\StoryFileUploadForm */
/** @var $coverUploadForm backend\models\StoryCoverUploadForm */
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?php if (!$model->isNewRecord): ?>
<?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
<?php endif ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
<?= $form->field($coverUploadForm, 'coverFile')->fileInput() ?>
<?php if (!empty($model->cover)): ?>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <a href="#" class="thumbnail"><img src="<?= StoryCover::getListThumbPath($model->cover) ?>" alt="..."></a>
    </div>
</div>
<?php endif ?>
<?= $form->field($fileUploadForm, 'storyFile')->fileInput() ?>
<?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'profileName'),
                                                          ['prompt' => 'Выбрать', 'readonly' => !Yii::$app->user->can('admin')]) ?>
<?php
$values = [];
foreach ($model->categories as $category) {
    $values[] = '<span class="label label-default">' . $category->name . '</span>';
}
$values = implode("\n", $values);
?>
<?php $input = '<div id="selected-category-list" style="margin: 10px 0">' . $values . '</div>'; ?>
<?= $form->field($model, 'story_categories', ['template' => "{label}\n{$input}\n{input}\n{hint}\n{error}"])
    ->hiddenInput()
    ->hint($this->render('_categories', [
        'selectInputID' => Html::getInputId($model, 'story_categories')
    ]), ['class' => false]) ?>

<?= $form->field($model, 'sub_access')->checkBox() ?>
<?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::class, [
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
<?= Html::submitButton(($model->isNewRecord ? 'Создать историю' : 'Сохранить изменения'), ['class' => 'btn btn-success', 'style' => 'margin-right: 20px']) ?>
</div>
<?php ActiveForm::end(); ?>
