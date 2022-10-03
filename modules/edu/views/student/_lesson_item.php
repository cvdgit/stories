<?php

declare(strict_types=1);

use modules\edu\models\EduLesson;
use yii\helpers\Url;

/**
 * @var EduLesson $model
 * @var int $studentId
 */
?>
<a href="<?= Url::to(['/edu/student/lesson', 'id' => $model->id]) ?>" class="col-sm-4 col-md-3 lesson-item__wrap">
    <div class="lesson-item">
        <div class="lesson-image">
            <img class="lesson-image__img" src="/school/img/logo.svg" alt="">
        </div>
        <div class="lesson-name">
            <div class="lesson-name__inner">
                <span><?= $model->name ?></span>
                <?php if (($finishedStories = $model->getStudentFinishedStoriesCount($studentId)) > 0): ?>
                <span style="color:rgba(9, 21, 38, 0.6);">
                    <?= $finishedStories ?>&nbsp;/&nbsp;<?= $model->getLessonStoriesCount() ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>
