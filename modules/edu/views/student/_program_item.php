<?php

declare(strict_types=1);

use modules\edu\models\EduClassProgram;
use yii\helpers\Url;

/**
 * @var EduClassProgram $model
 * @var int $classId
 * @var int $studentId
 */
?>
<div class="col-sm-4 col-md-3">
    <a href="<?= Url::to($model->createTopicRoute($classId)) ?>" class="thumbnail panel-wrap">
        <div class="panel-img"></div>
        <div class="panel-inner">
            <div class="panel-header">
                <span class="panel-header__text"><?= $model->program->name ?></span>
            </div>
        </div>
        <div class="panel-progress">
            <div class="progress-chart">
                <?php if (($progress = $model->getStudentProgress($studentId)) > 0): ?>
                <div><?= $progress ?> %</div>
                <?php endif ?>
            </div>
            <div class="progress-text">Пройдено историй <?= $model->getStudentFinishedStoriesCount($studentId) ?> из <?= $model->getClassProgramStoriesCount() ?></div>
        </div>
    </a>
</div>
