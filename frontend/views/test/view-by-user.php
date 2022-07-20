<?php
use frontend\assets\TestAsset;
use yii\helpers\Html;
/* @var $model common\models\StoryTest */
/* @var $userId int */
TestAsset::register($this);
?>
<div class="modal-body">
    <div class="story-container">
        <div class="story-container-inner" id="story-container">
            <section class="run-test">
                <?= Html::tag('div', '', ['class' => 'new-questions', 'data-test-id' => $model->id, 'data-user-id' => $userId]) ?>
            </section>
        </div>
    </div>
</div>
