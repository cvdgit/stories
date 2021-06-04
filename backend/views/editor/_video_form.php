<?php
use common\models\SlideVideo;
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\VideoForm */
$form->action = ['editor/update-block/video'];
?>
<div class="row">
    <div class="col-xs-6">
        <?php
        $items = [];
        if ($model->sourceIsYouTube()) {
            $items = SlideVideo::videoArray();
        }
        if ($model->sourceIsFile()) {
            $items = SlideVideo::videoFileArray();
        }
        ?>
        <?= $form->field($model, 'video_id', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList($items, ['prompt' => 'Выбрать видео']) ?>
    </div>
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
        <?= $form->field($model, 'to_next_slide', ['inputOptions' => ['class' => 'form-control input-sm']])
            ->checkbox()
            ->hint('После завершения воспроизведения') ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'volume', ['inputOptions' => ['class' => 'form-control input-sm']])
            ->textInput()
            ->hint('0 - без звука; 1 - максимальная громкость') ?>
    </div>
</div>
