<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\helpers\Url;

/**
 * @var array $model
 * @var int $classId
 * @var int $studentId
 */

$obj = $model['obj'];
$route = ['/edu/repetition/quiz', 'id' => $model['id']];
$title = 'Тестирование';
if ($obj === 'mental_map') {
    $route = ['/edu/repetition/mental-map', 'id' => $model['id']];
    $title = 'Ментальная карта';
}
?>
<div class="col-sm-4 col-md-3">
    <a href="<?= Url::to($route); ?>" class="thumbnail panel-wrap">
        <div class="panel-img" style="position: relative"><span class="badge" style="position: absolute; left: 30px; top: -45px"><?= $title ?></span></div>
        <div class="panel-inner">
            <div class="panel-header">
                <span title="<?= $model['header']; ?>" class="panel-header__text" style="text-overflow: ellipsis; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical"><?= $model['header']; ?></span>
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
