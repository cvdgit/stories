<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */
?>
<div class="header-block">
    <h2 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2">Обязательные истории</h2>
</div>
<div class="story-list" style="margin-bottom: 40px">
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'itemView' => '_required_story_item',
        'itemOptions' => ['tag' => false],
        'layout' => "{summary}\n<div class=\"row flex-row\">{items}</div>",
    ]) ?>
</div>
