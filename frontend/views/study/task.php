<?php
use common\helpers\Url;
/** @var $taskModel common\models\StudyTask */
/** @var $model common\models\Story */
/** @var $userProgress common\models\StudyTaskProgress */
$title = $model->title;
$this->setMetaTags($title, $model->description, $title, $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
/** @var $this yii\web\View */
?>
<div class="container story-head-container">
    <main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner" id="story-container">
                <div class="story-no-subscription">
                    <?= $this->render('_form', ['taskModel' => $taskModel, 'userProgress' => $userProgress]) ?>
                </div>
            </div>
        </div>
    </main>
</div>