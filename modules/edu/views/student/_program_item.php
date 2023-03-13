<?php

declare(strict_types=1);

use modules\edu\models\EduClassProgram;
use yii\helpers\Url;

/**
 * @var EduClassProgram $model
 * @var int $studentId
 */

$route = $model->createTopicRoute();
?>
<div class="col-sm-4 col-md-3">
    <?php if ($route === null): ?>
    <div class="thumbnail panel-wrap">
        <div class="panel-img"></div>
        <div class="panel-inner">
            <div class="panel-header">
                <span class="panel-header__text"><?= $model->program->name ?></span>
            </div>
        </div>
        <div class="panel-progress">
            <div class="progress-chart"></div>
            <div class="progress-text">Нет тем</div>
        </div>
    </div>
    <?php else: ?>
    <a href="<?= Url::to($route); ?>" class="thumbnail panel-wrap">
        <div class="panel-img"></div>
        <div class="panel-inner">
            <div class="panel-header">
                <span class="panel-header__text"><?= $model->program->name ?></span>
            </div>
        </div>
        <div class="panel-progress">
            <div class="progress-chart">
                <?php if (($progress = $model->getStudentProgress($model->getClassProgramStoriesCount(), $model->getStudentFinishedStoriesCount($studentId))) > 0): ?>
                <div><?= $progress ?> %</div>
                <?php endif ?>
            </div>
            <div class="progress-text">Пройдено историй <?= $model->getStudentFinishedStoriesCount($studentId) ?> из <?= $model->getClassProgramStoriesCount() ?></div>
        </div>
    </a>
    <?php endif; ?>
</div>
