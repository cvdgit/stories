<?php

declare(strict_types=1);

use modules\edu\models\EduLesson;
use yii\helpers\Url;

/**
 * @var EduLesson $model
 */
?>
<div class="col-sm-6 col-md-4">
    <a href="<?= Url::to(['/edu/student/lesson', 'id' => $model->id]) ?>" class="thumbnail">
        <div class="caption">
            <h3><?= $model->name ?></h3>
            <p>...</p>
            <p>...</p>
        </div>
    </a>
</div>
