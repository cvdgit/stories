<?php

declare(strict_types=1);

use backend\actions\ReplaceVideo\ReplaceVideoForm;
use backend\actions\ReplaceVideo\VideoDto;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var list<VideoDto> $storyVideos
 * @var ReplaceVideoForm $formModel
 * @var array<int, string> $videoItems
 */
?>
<div>
    <h3 class="h4">Видео из истории:</h3>
    <?php foreach ($storyVideos as $video): ?>
    <div>
        <?= $video->getName(); ?>
    </div>
    <?php endforeach; ?>

    <div style="margin-top: 20px">
        <?php $form = ActiveForm::begin(['id' => 'video-replace-form']); ?>
        <?= $form->field($formModel, 'replace_video_id')->dropDownList($videoItems, ['prompt' => 'Выберите видео']); ?>
        <div style="padding: 20px 0; text-align: center">
            <?= Html::submitButton('Заменить', ['class' => 'btn btn-primary']); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
