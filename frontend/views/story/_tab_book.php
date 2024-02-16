<?php

declare(strict_types=1);

use yii\web\View;
use common\models\Story;

/**
 * @var View $this
 * @var Story $model
 * @var string $guestStoryBody
 */
?>
<div class="slides-readonly">
    <?php if ($model->isAudioStory()): ?>
    <div class="alert alert-info to-slides-tab noselect">
        <p>Озвучка доступна в режиме обучения</p>
    </div>
    <?php endif ?>
    <?php if (!empty($guestStoryBody)): ?>
        <?= $guestStoryBody ?>
    <?php else: ?>
        <p>Содержимое истории недоступно</p>
    <?php endif ?>
</div>
