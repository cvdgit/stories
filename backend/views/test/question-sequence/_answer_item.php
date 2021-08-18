<?php
use yii\helpers\Html;
/** @var $answerModel backend\models\question\sequence\SequenceAnswerForm */
?>
<div class="media" data-answer-id="<?= $answerModel->id ?>">
    <div class="media-left">
        <i class="glyphicon glyphicon-move handle"></i>
    </div>
    <div class="media-left">
        <div class="dm-uploader">
            <div class="btn">
                <?= Html::img($answerModel->hasImage() ? $answerModel->imagePath : '/img/image-placeholder-white.svg', [
                    'class' => 'media-object' . ($answerModel->hasImage() ? '' : ' no-image'),
                    'style' => 'width: 110px;height:100px'
                ]) ?>
                <input type="file" style="height: 100%">
                <div class="file-loading"></div>
            </div>
        </div>
    </div>
    <div class="media-body">
        <h4 class="media-heading"><?= Html::encode($answerModel->name) ?></h4>
    </div>
    <div class="media-right">
        <a href="#" class="delete-answer pull-right"><i class="glyphicon glyphicon-trash"></i></a>
    </div>
</div>