<?php

declare(strict_types=1);

use common\assets\StoryPluginsAsset;
use common\helpers\Url;
use common\rbac\UserRoles;
use frontend\assets\MentalMapAsset;
use frontend\assets\RetellingAsset;
use frontend\assets\SlidesAsset;
use frontend\assets\TestAsset;
use modules\edu\models\EduStory;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var EduStory $story
 * @var int $programId
 * @var array $backRoute
 * @var int $studentId
 * @var array $contentMentalMaps
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
        'student_id' => $studentId,
    ],
    'mental_map' => [
        'story_id' => $storyId,
        'student_id' => $studentId,
    ],
    'retelling' => [
        'story_id' => $storyId,
        'student_id' => $studentId,
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
        'student_id' => $studentId,
    ],
    'next-story' => [
        'story_id' => $storyId,
        'program_id' => $programId,
        'student_id' => $studentId,
    ],
    'content-mental-map' => [
        'story_id' => $storyId,
        'mentalMaps' => $contentMentalMaps,
        'mapOrder' => [
            'mental-map',
            'mental-map-even-fragments',
            'mental-map-odd-fragments',
            'mental-map-plan',
            'mental-map-plan-accumulation',
        ],
    ],
    'table-of-contents' => [
        'storyId' => $storyId,
        'userId' => Yii::$app->user->id,
    ],
];

$configJson = Json::encode($config);

$this->registerJs(<<<JS
window.WikidsRevealConfig = $configJson;
JS
);

StoryPluginsAsset::register($this);
TestAsset::register($this);
MentalMapAsset::register($this);
RetellingAsset::register($this);
SlidesAsset::register($this);

$this->registerCss(<<<CSS
.course-header-wrapper {
    height: 50px;
    position: relative;
}
.course-header {
    background: #fff;
    border-bottom: 0.1rem solid #eee;
}
.course-header-wrap {
    display: flex;
    position: relative;
    height: 50px;
    padding-right: 1.3rem;
    padding-left: 1.5rem;
}
.course-header-inner {
    display: flex;
    align-items: center;
    height: 5rem;
    flex: 0 0 auto;
}
.leave-course-button {
    color: #313537;
    --icon-color: #313537;
    display: flex;
    align-items: center;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
    border: none;
    padding: 0;
    background: transparent;
    text-transform: uppercase;
    letter-spacing: 0.1rem;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: 900;
    text-decoration: none;
}
CSS
);

$this->title = $story->title;
?>
<div class="container-fluid">
    <div class="course-header-wrapper">
        <div class="course-header">
            <div class="course-header-wrap">
                <div class="course-header-inner">
                    <a href="<?= Url::to($backRoute) ?>" class="leave-course-button"><i style="font-size: 12px" class="glyphicon glyphicon-chevron-left"></i> Назад</a>
                </div>
                <div></div>
            </div>
        </div>
    </div>
</div>

<div class="story-box story-container-wrap" style="height: auto; position: relative">
    <div class="story-container">
        <div class="story-container-inner" id="story-container">
            <div class="reveal" data-toggle="slides">
                <?= $story->slidesData() ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {
    let deck = initSlides();
})();
JS
);
