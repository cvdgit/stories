<?php
use yii\helpers\Html;
/* @var $model common\models\StoryTest */
/* @var $userId int */
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