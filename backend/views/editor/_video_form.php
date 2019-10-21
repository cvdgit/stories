<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\VideoForm */

$form->action = ['/editor/update-video'];
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'video_id')->dropDownList(common\models\SlideVideo::videoArray(), ['prompt' => 'Выбрать видео']) ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'seek_to')->textInput() ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'mute')->checkbox() ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'duration')->textInput() ?></div>
</div>
