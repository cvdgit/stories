<?php

declare(strict_types=1);

use frontend\widgets\EduRevealWidget;

/**
 * @var \yii\web\View $this
 * @var \common\models\Story $story
 */

?>
<div class="story-head-container">
    <main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner" id="story-container">
                <div class="story-no-subscription">
                    <?= $this->render('_player', ['model' => $story]) ?>
                </div>
            </div>
        </div>
    </main>
</div>
