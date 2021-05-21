<?php
/** @var $model backend\models\video\CreateFileVideoForm */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Новое видео из файла';
$this->params['breadcrumbs'] = [
    ['label' => 'Видео', 'url' => ['video/index', 'source' => $model->source]],
    $this->title,
];
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'videoFile')->fileInput() ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
