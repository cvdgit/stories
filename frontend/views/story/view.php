<?php

use frontend\widgets\StoryAudio;
use frontend\widgets\StoryFavorites;
use frontend\widgets\StoryLikeWidget;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $userCanViewStory bool */
/* @var $commentForm frontend\models\CommentForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

\frontend\assets\LazyAsset::register($this);

$title = $model->title;
$this->setMetaTags($title,
                   $model->description,
                   $model->title . ', ' . $model->title . ' сказка на ночь',
                   $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

/** @var $trackID int? */
$action = Url::to(['story/init-story-player', 'id' => $model->id, 'track_id' => $trackID]);

$isGuest = var_export(Yii::$app->user->isGuest, true);

/** @var $storyDefaultView string */
$js = <<< JS

var isGuest = $isGuest;

var lazy = $(".lazy").Lazy({
    scrollDirection: "vertical",
    effect: "fadeIn",
    visibleOnly: true,
    chainable: false
});

if ($("#story_wrapper").is(":visible")) {
    WikidsStory.loadStory("$action");
}

$('#comment-form-pjax').on('pjax:success', function() {
    $.pjax.reload({container: '#comment-list-pjax'});
});

/*
if (Wikids2.showSwipeHelp()) {
    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-center",
      "preventDuplicates": false,
      "onclick": function() {
          Wikids2.hideSwipeHelp();
      },
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": 0,
      "extendedTimeOut": 0,
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut",
      "tapToDismiss": false
    };
    toastr["info"]("Чтобы перейти к следующему слайду проведите пальцем справа-налево");
}*/

var inSlides = false;
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    var view = e.target.getAttribute("href").substr(1);
    if (view === "tab-slides") {
        inSlides = true;
        $(".tab-slides").popover("hide");
        WikidsStory.loadStory("$action");
        ym(53566996, 'reachGoal', 'transition_to_training');
    }
    else if (view === "tab-book") {
        lazy.update(false);
    }
    else {

    }
});

$(".tab-slides").popover({
    container: "#w0",
    title: "Попробуйте режим обучения",
    content: '<ul>' +
'<li>просмотр истории в виде слайдов</li>' +
'<li>возможность прослушивания озвучки по каждому слайду</li>' +
'<li>возможность добавить свою, детскую озвучку</li>' +
'<li>тесты для детей, чтобы закрепить материал</li>' +
'<li>специально подобранные коллекции картинок и видео для улучшения восприятия</li>' +
'<li>ссылки на дополнительные обучающие курсы</li>' +
'</ul>',
    html: true,
    placement: "bottom",
    trigger: isGuest ? "manual" : "hover"
});
    
if (isGuest) {    
    $("#w0").on("click", ".popover", function() {
        $(".tab-slides").popover("hide");
    });
    
    setTimeout(function() {
        if (!inSlides) {
            $(".tab-slides").popover("show");
        }
    }, 2000);
}

 $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
        $('#back-to-top').fadeIn();
    } else {
        $('#back-to-top').fadeOut();
    }
 });
// scroll body to 0px on click
$('#back-to-top').click(function () {
    $('#back-to-top').tooltip('hide');
    $('body,html').animate({
        scrollTop: 0
    }, 800);
    return false;
});

$('#back-to-top').tooltip('show');

JS;
$this->registerJs($js);

$css = <<< CSS
.back-to-top {
    cursor: pointer;
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none;
}
CSS;
$this->registerCss($css);

$isSlidesView = $storyDefaultView === 'slides';
$isBookView = $storyDefaultView === 'book';
?>
<div class="container story-head-container">
	<main class="site-story-main">
        <div style="padding: 0 15px">
            <h1><?= Html::encode($model->title) ?></h1>
            <div class="story-description">
                <div class="story-categories">
                    <?php foreach ($model->categories as $category): ?>
                    <?= Html::a($category->name, ['story/category', 'category' => $category->alias]) ?>
                    <?php endforeach ?>
                </div>
                <?php if (!empty($model->description)): ?>
                    <div class="story-text"><?= Html::encode($model->description) ?></div>
                <?php endif ?>
                <?php $facts = $model->storyFacts(); ?>
                <?php if (count($facts) > 0): ?>
                <div class="story-facts" style="font-size: 1.5rem">
                    Из истории вы узнаете про:
                <?php foreach ($facts as $fact): ?>
                    <span class="label label-success"><?= $fact['title'] ?></span>
                <?php endforeach ?>
                </div>
                <?php endif ?>
            </div>
        </div>
        <div class="tabbable-panel">
            <div class="tabbable-line">
            <?= Tabs::widget([
                'class' => 'profile-tabs',
                'items' => [
                    [
                        'label' => 'Режим обучения',
                        'content' => $this->render('_tab_slides', ['model' => $model]),
                        'active' => $isSlidesView,
                        'options' => ['id' => 'tab-slides'],
                        'linkOptions' => ['class' => 'tab-slides'],
                    ],
                    [
                        'label' => 'Режим чтения',
                        'content' => $this->render('_tab_book', ['model' => $model]),
                        'active' => $isBookView,
                        'options' => ['id' => 'tab-book'],
                    ],
                ],
            ]) ?>
            </div>
        </div>
    </main>
</div>
<div class="container">
    <main class="site-story-main">
        <div class="story-description" style="margin-top: 10px">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-12">
                    <div class="story-tags">
                        <!--noindex-->
                        <?php foreach($model->tags as $tag): ?>
                            <?= '#' . Html::a($tag->name, ['tag', 'tag' => $tag->name], ['rel' => 'nofollow']) ?>
                        <?php endforeach ?>
                        <!--/noindex-->
                    </div>
                    <div class="story-date"><span>Опубликована:</span> <?= \common\helpers\SmartDate::dateSmart($model->created_at, true) ?></div>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12">
                    <div class="story-share-block">
                        <?= StoryLikeWidget::widget(['storyId' => $model->id]) ?>
                        <button class="btn-share" title="Поделиться" data-toggle="modal" data-target="#wikids-share-modal"><i class="glyphicon glyphicon-share"></i></button>
                        <?= StoryFavorites::widget(['storyId' => $model->id]) ?>
                    </div>
                </div>
            </div>
	    </div>
	    <div class="comments">
            <div class="row">
                <div class="col-md-9">
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <?= $this->render('_comment_form', ['commentForm' => $commentForm]) ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center comment-guest-info">
                            Чтобы оставить комментарий <a href="#" data-toggle="modal" data-target="#wikids-signup-modal">зарегистрируйтесь</a> или <a href="#" data-toggle="modal" data-target="#wikids-login-modal">войдите</a> в аккаунт
                        </div>
                    <?php endif ?>
                    <div class="comment-list">
                        <?= $this->render('_comment_list', ['dataProvider' => $dataProvider]) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="following-stories">
                        <h2>Смотрите также:</h2>
                        <?= \frontend\widgets\FollowingStories::widget(['storyID' => $model->id]) ?>
                    </div>
                </div>
            </div>

	    </div>
        <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" title="Нажмите, чтобы подняться на верх" data-toggle="tooltip" data-placement="left"><span class="glyphicon glyphicon-chevron-up"></span></a>
	</main>
</div>
<?= \frontend\widgets\Share::widget(['story' => $model]) ?>
