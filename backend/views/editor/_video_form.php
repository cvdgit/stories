<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\VideoForm */

$form->action = ['/editor/update-video'];
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'video_id', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList(common\models\SlideVideo::videoArray(), ['prompt' => 'Выбрать видео']) ?></div>
    <div class="col-xs-6">
        <?= $form->field($model, 'speed', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList(\backend\models\editor\VideoForm::videoSpeedArray()) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'seek_to', ['inputOptions' => ['class' => 'form-control input-sm']])
            ->textInput()
            ->hint('<a href="#" onclick="WikidsVideo.setBeginVideo(this); return false">задать начало</a>') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'duration', ['inputOptions' => ['class' => 'form-control input-sm']])
            ->textInput()
            ->hint('<a href="#" onclick="WikidsVideo.setEndVideo(this, \'videoform-seek_to\'); return false">задать окончание</a>') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'mute', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
    </div>
</div>
