<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model backend\models\video\CreateVideoForm */

$this->title = 'Новое видео';
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'video_id')->textInput() ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
