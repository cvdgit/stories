<?php

declare(strict_types=1);

use common\components\StoryCover;
use common\models\Story;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Story $model
 * @var int $studentId
 * @var int $programId
 */

$progress = $model->findStudentStoryProgress($studentId);
$status = 'to-learn';
if ($progress && $progress->statusIsDone()) {
    $status = 'is-done';
}
?>
<div class="col-lg-3 col-md-4 col-sm-6">
    <div class="story-item edu-story-item <?= $status ?>">
        <a class="run-story" href="<?= Url::toRoute(['/edu/story/view', 'id' => $model->id, 'program_id' => $programId]) ?>" data-pjax="0">
            <div class="story-item-image">
                <?php $img = empty($model->cover) ? '/img/story-1.jpg' : StoryCover::getListThumbPath($model->cover); ?>
                <div class="story-image" style="background-image: url(<?= $img ?>)">
                    <div class="story-image__icon"></div>
                </div>
                <div class="story-item-image-overlay">
                    <span></span>
                </div>
            </div>
            <div class="story-item-caption">
                <p class="flex-text"></p>
                <p><h3 class="story-item-name"><?= Html::encode($model->title) ?></h3></p>
            </div>
        </a>
    </div>
</div>
