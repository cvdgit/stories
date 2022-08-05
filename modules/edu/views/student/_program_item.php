<?php

declare(strict_types=1);

use modules\edu\models\EduProgram;
use yii\helpers\Url;

/**
 * @var EduProgram $model
 * @var int $classId
 */
?>
<div class="col-sm-6 col-md-4">
    <a href="<?= Url::to($model->createTopicRoute($classId)) ?>" class="thumbnail">
        <div class="caption">
            <h3><?= $model->name ?></h3>
            <p>...</p>
            <p>...</p>
        </div>
    </a>
</div>
