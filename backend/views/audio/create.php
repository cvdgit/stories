<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Новая аудио дорожка';

/** @var $this yii\web\View */
/** @var $model backend\models\audio\CreateAudioForm */
/** @var $audioUploadForm backend\models\audio\AudioUploadForm */
?>
<div class="row">
    <div class="col-md-6">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'type')->dropDownList(\common\models\StoryAudioTrack::audioTypeArray()) ?>
        <?= $form->field($model, 'default')->checkbox() ?>
        <?= $form->field($audioUploadForm, 'audioFiles[]')->fileInput(['multiple' => true, 'accept' => '.mp3,audio/*']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
