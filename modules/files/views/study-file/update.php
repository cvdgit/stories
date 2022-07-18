<?php
use modules\files\forms\UpdateStudyFileForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
/**
 * @var $this yii\web\View
 * @var $fileModel modules\files\models\StudyFile
 * @var $model UpdateStudyFileForm
 */
$this->title = 'Файл: ' . $fileModel->name;
$this->params['breadcrumbs'][] = ['label' => 'Файлы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $fileModel->name;
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= Html::encode($this->title) ?></h1>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="study-file-update">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'file')->fileInput() ?>
            <div style="padding: 20px 0">
                <a href="<?= $fileModel->getFileLinkBackend() ?>"><?= $fileModel->name ?></a>
            </div>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'folder_id')->dropdownList($model->getFolderItems(), ['prompt' => 'Выберите папку']) ?>
            <?= $form->field($model, 'status')->dropdownList($model->getStatusItems()) ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-primary my-2']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
