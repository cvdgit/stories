<?php

declare(strict_types=1);

use backend\assets\TestAsset;
use common\models\UserStudent;
use frontend\assets\SlidesAsset;
use modules\edu\models\EduLesson;
use modules\edu\models\EduTopic;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/**
 * @var UserStudent $student
 * @var DataProviderInterface $dataProvider
 * @var EduTopic[] $topics
 * @var string $classProgramName
 * @var View $this
 * @var EduLesson $lesson
 * @var int $currentTopicId
 * @var int $programId
 * @var string $studentToolbarWidget
 */

$this->title = $student->name;

TestAsset::register($this);
SlidesAsset::register($this);

$this->registerCss(<<<CSS
#run-story-modal .modal-body {
    padding: 0;
}
@media (min-width: 992px) {
    #run-story-modal .modal-lg {
        width: 1300px;
    }
}
.story-image {
    width: 100%;
    display: flex;
    flex: 0 0 auto;
    background-color: rgb(255, 255, 255);
    height: 160px;
    border-radius: 16px;
    box-sizing: border-box;
    background-size: 100% 100%;
    background-position: center center;
    background-repeat: no-repeat;
    position: relative;
    top: 0;
    left: 0;
}
.edu-story-item.is-done .story-image {
    border: 4px #99cd50 solid;
}
.edu-story-item.is-done .story-image .story-image__icon {
    width: 40px;
    height: 40px;
    position: absolute;
    top: 0;
    right: 0;
    border-radius: 50%;
    background-size: 100%;
    background-position: center center;
    background-repeat: no-repeat;
    transform: translate(33%, -33%);
    background-color: rgb(28, 198, 127);
    z-index: 999;
    background-image: url(/school/img/done_white.svg);
}
.edu-story-item.to-learn .story-image {
    border: 4px #4574EE solid;
}
.edu-story-item.to-learn .story-image .story-image__icon {
    width: 40px;
    height: 40px;
    position: absolute;
    top: 0;
    right: 0;
    border-radius: 50%;
    background-size: 100%;
    background-position: center center;
    background-repeat: no-repeat;
    transform: translate(33%, -33%);
    background-color: rgb(128, 62, 241);
    z-index: 999;
    background-image: url(/school/img/play_white.svg);
}
.story-list .edu-story-item .story-item-image-overlay {
    border-radius: 16px;
}
CSS
);
?>
<div class="container">
    <?= $studentToolbarWidget; ?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="h2" style="margin-top: 0; margin-bottom: 20px"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/student/index']) ?> <?= $classProgramName ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <?= $this->render('_all_topics', ['topics' => $topics, 'currentTopicId' => $currentTopicId]) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top:0">

            <h2 class="h3" style="margin-top:0;margin-bottom:2rem"><?= Html::encode($lesson->name) ?></h2>

            <div class="story-list-wrap">

            <?php Pjax::begin(['id' => 'pjax-stories']) ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'itemView' => '_story_item',
                'viewParams' => ['studentId' => $student->id, 'programId' => $programId],
                'itemOptions' => ['tag' => false],
                'layout' => "{summary}\n<div class=\"story-list\"><div class=\"flex-row row\">{items}</div></div>\n{pager}",
            ]) ?>
            <?php Pjax::end() ?>

            </div>
        </div>
    </div>
</div>

<div class="modal remote fade modal-fullscreen" id="run-story-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {
/*
$('.story-list-wrap').on('click', '.run-story', function(e) {
    e.preventDefault();
    $('#run-story-modal')
        .modal({'remote': $(this).attr('href')});
});

let deck;

$('#run-story-modal')
    .on('loaded.bs.modal', function() {
        deck = initSlides();
    })
    .on('hide.bs.modal', function() {

        if (deck) {
            deck.destroy();
        }

        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');

        $.pjax.reload({container: '#pjax-stories', async: false});
    });*/

})();
JS
);
