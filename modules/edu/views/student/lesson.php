<?php

declare(strict_types=1);

use common\models\UserStudent;
use frontend\assets\SlidesAsset;
use modules\edu\models\EduLesson;
use modules\edu\models\EduTopic;
use modules\edu\widgets\StudentToolbarWidget;
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
 */

$this->title = $student->name;

SlidesAsset::register($this);
?>
<div class="container">

    <?= StudentToolbarWidget::widget(['student' => $student]) ?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="h2" style="margin-top: 0; margin-bottom: 20px"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/student/index']) ?> <?= $classProgramName ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <?= $this->render('_all_topics', ['topics' => $topics]) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top:0">

            <h3 style="margin-top:0"><?= Html::encode($lesson->name) ?></h3>

            <?php Pjax::begin(['id' => 'pjax-stories']) ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'itemView' => '_story_item',
                'itemOptions' => ['tag' => false],
                'layout' => "{summary}\n<div class=\"story-list\"><div class=\"flex-row row\">{items}</div></div>\n{pager}",
            ]) ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

<div class="modal remote fade modal-fullscreen" id="run-story-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {

$('.story-list').on('click', '.run-story', function(e) {
    e.preventDefault();
    $('#run-story-modal')
        .modal({'remote': $(this).attr('href')});
});



$('#run-story-modal')
    .on('hide.bs.modal', function() {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    })
    .on('loaded.bs.modal', function() {
        initSlides();
    });

})();
JS
);
