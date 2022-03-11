<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
$this->title = 'Новая аудио дорожка';

/** @var $storyModel common\models\Story */
$this->params['sidebarMenuItems'] = [
    ['label' => 'Вернуться к списку', 'url' => ['audio/index', 'story_id' => $storyModel->id]],
];

/** @var $model backend\models\audio\UpdateAudioForm */
?>
<div class="row">
    <div class="col-md-6">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'type')->dropDownList(\common\models\StoryAudioTrack::audioTypeArray()) ?>
        <?= $form->field($model, 'default')->checkbox() ?>
        <?= $form->field($model->audioUploadForm, 'audioFiles[]')->fileInput(['multiple' => true, 'accept' => '.mp3,audio/*']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-4">
        <?= $this->render('_audio_files', ['audioUploadForm' => $model->audioUploadForm, 'model' => $storyModel, 'trackModel' => $model->getTrack()]) ?>
    </div>
</div>