<?php
use frontend\assets\MobileTestAsset;
use yii\helpers\Html;
MobileTestAsset::register($this);
$this->title = 'Mobile testing';
/** @var $model common\models\StoryTest */
/** @var $studentId int */
?>
<div class="container" style="margin-bottom: 50px">
    <div class="row">
        <div class="col-md-12">
            <?= Html::tag('div', '', [
                'data-toggle' => 'mobile-testing',
                'class' => 'new-questions',
                'data-test-id' => $model->id,
                'data-student-id' => $studentId,
            ]) ?>
        </div>
    </div>
</div>