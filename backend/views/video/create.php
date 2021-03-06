<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model backend\models\video\CreateVideoForm */
$this->title = 'Новое видео';
$this->params['breadcrumbs'] = [
    ['label' => 'Видео', 'url' => ['video/index']],
    $this->title,
];
?>
<div>
    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'video_id')->textInput() ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
