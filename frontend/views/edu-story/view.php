<?php

declare(strict_types=1);

use common\helpers\Url;
use common\models\Story;
use common\rbac\UserRoles;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var Story $story
 */

$storyId = $story->id;
$config = [
    'feedback' => [
        'action' => Url::to(['/feedback/create', 'id' => $storyId])
    ],
    'transition' => [
        'action' => Url::to(['/story/get-story-body']),
        'getSlideAction' => Url::to(['/player/get-slide']),
        'story_id' => $storyId,
    ],
    'testing' => [
        'action' => Url::to(['/story/get-story-test']),
        'storeAction' => Url::to(['/story/store-test-result', 'story_id' => $storyId]),
        'storyBodyAction' => Url::to(['/story/get-story-body']),
        'story_id' => $storyId,
        'initAction' => Url::to(['/question/init']),
        'student_id' => Yii::$app->studentContext->getId(),
    ],
    'links' => [
        'story_id' => $storyId,
        'links' => $story->slideBlocksData(),
    ],
    'video' => [
        'showControls' => UserRoles::isModerator(Yii::$app->user->id),
    ],
    'slide_links' => [
        'site' => Url::getServerUrl() . '/story/',
    ],
    'stat' => [
        'story_id' => $storyId,
        'action' => Url::to(['/statistics/write-edu']),
        'student_id' => Yii::$app->studentContext->getId(),
    ],
];

$configJson = Json::encode($config);

$this->registerJs(<<<JS
window.WikidsRevealConfig = $configJson;
JS
);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Html::encode($story->title) ?></h4>
</div>
<div class="modal-body">
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
</div>
<div class="modal-footer"></div>
