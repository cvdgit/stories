<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\helpers\Url;

/**
 * @var array $model
 * @var int $classId
 * @var int $studentId
 */
?>
<div class="col-sm-4 col-md-3">
    <a href="<?= Url::to(['/edu/repetition/view', 'id' => $model['id']]); ?>" class="thumbnail panel-wrap">
        <div class="panel-img"></div>
        <div class="panel-inner">
            <div class="panel-header">
                <span class="panel-header__text"><?= $model['header']; ?></span>
            </div>
        </div>
        <div class="panel-progress">
            <div class="progress-chart">
                <div><?= $model['doneItems']; ?> из <?= $model['totalItems']; ?></div>
            </div>
            <div class="progress-text"><?= SmartDate::dateSmart($model['date'], true); ?></div>
        </div>
    </a>
</div>
