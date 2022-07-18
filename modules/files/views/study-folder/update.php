<?php
use modules\files\forms\FilesUploadForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
/**
 * @var $this yii\web\View
 * @var $model modules\files\models\StudyFolder
 * @var $uploadModel FilesUploadForm
 * @var $files array
 */
$this->title = 'Папка: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Папки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= Html::encode($this->title) ?></h1>
</div>
<div class="row">
    <div class="col-6">
        <div class="study-folder-update">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    <div class="col-6">
        <div class="mb-3">
            <h3 class="h4">Загрузить файлы</h3>
            <?php $form = ActiveForm::begin(['action' => ['study-folder/upload-files', 'id' => $model->id], 'options' => ['enctype' => 'multipart/form-data']]) ?>
            <?= $form->field($uploadModel, 'files[]')->fileInput(['multiple' => true]) ?>
            <?= Html::submitButton('Загрузить файлы', ['class' => 'btn btn-secondary']) ?>
            <?php ActiveForm::end() ?>
        </div>
        <div>
            <h3 class="h4">Файлы</h3>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Название</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?= Html::a($file->name, ['study-file/update', 'id' => $file->id]) ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
