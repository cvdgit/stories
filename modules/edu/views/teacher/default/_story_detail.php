<?php

declare(strict_types=1);

use modules\edu\assets\RevealAsset;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\SlideRetelling;
use modules\edu\query\GetStoryTests\SlideTest;
use yii\web\View;

/**
 * @var View $this
 * @var array $history
 */

RevealAsset::register($this);
$this->registerCss($this->renderFile('@modules/edu/views/teacher/default/_story_detail.css'));
$this->registerJs($this->renderFile('@modules/edu/views/teacher/default/_story_detail.js'));

$byTypeViewMap = [
    Slide::class => '_slide',
    SlideMentalMap::class => '_slide_mental_map',
    SlideTest::class => '_slide_test',
    SlideRetelling::class => '_slide',
];
?>
<div style="display: flex; flex-direction: column; gap: 20px; padding: 20px 0">
    <?php
    foreach ($history as $historyItem): ?>
        <?php
        $type = $historyItem['type'];
        echo $this->render($byTypeViewMap[$type], ['historyItem' => $historyItem]);
        ?>
    <?php
    endforeach; ?>
</div>
