<?php

declare(strict_types=1);

use common\models\Story;
use frontend\assets\SlidesAsset;
use frontend\widgets\EduRevealWidget;
use yii\web\View;

/**
 * @var View $this
 * @var Story $story
 */
?>
<div class="story-head-container">
    <main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner" id="story-container">
                <div class="story-no-subscription">
                    <div class="reveal" data-toggle="slides">
                        <?= $story->slidesData() ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
