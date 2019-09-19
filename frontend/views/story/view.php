<?php

use frontend\widgets\StoryAudio;
use frontend\widgets\StoryFavorites;
use frontend\widgets\StoryLikeWidget;
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

$action = Url::to(['story/init-story-player', 'id' => $model->id]);
/** @var $storyDefaultView string */
$js = <<< JS

var lazy = $(".lazy").Lazy({
    scrollDirection: "vertical",
    effect: "fadeIn",
    visibleOnly: true,
    chainable: false
});

var defaultView = "$storyDefaultView";
if ($("#story_wrapper").is(":visible")) {
    Wikids2.loadStory("$action");
    $("[data-story-view]").removeClass("active");
    $("[data-story-view=" + defaultView + "]").addClass("active");
}

function switchStoryView(view) {
    if (view === "slides") {
        $(".slides-readonly").hide();
        $("#story_wrapper").show();
        Wikids2.loadStory("$action");
    }
    else if (view === "book") {
        $(".slides-readonly").show();
        $("#story_wrapper").hide();
        lazy.update(false);
    }
    else {

    }
}

$("[data-story-view]").on("click", function(e) {
    e.preventDefault();
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    var view = $(this).attr("data-story-view");
    switchStoryView(view);
});

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

JS;
$this->registerJs($js);

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
            </div>
            <div class="story-view-mode clearfix">
                <a class="active" href="#" data-story-view="book" title="Просмотр в режиме чтения">
                    <i class="glyphicon glyphicon-book"></i>
                    <span>Режим чтения</span>
                </a>
                <a href="#" data-story-view="slides" title="Просмотр в режиме обучения">
                    <i class="glyphicon glyphicon-education"></i>
                    <span>Режим обучения</span>
                </a>
            </div>
        </div>
        <div class="slides-readonly" style="<?= $isBookView ? '' : 'display: none' ?>">
            <?php if ($model->isAudioStory()): ?>
            <?= StoryAudio::widget(['storyID' => $model->id]) ?>
            <?php endif ?>
            <?php if (!empty($model->body)): ?>
            <?= $model->body ?>
            <?php else: ?>
            <p>Содержимое истории недоступно</p>
            <?php endif ?>
        </div>
        <div id="story_wrapper" style="<?= $isSlidesView ? '' : 'display: none' ?>">
            <div class="story-container">
                <div class="story-container-inner" id="story-container">
                    <div class="story-no-subscription"><span class="story-loader">Загрузка истории...</span></div>
                </div>
            </div>
            <?php if (Yii::$app->user->can('moderator')): ?>
            <?= $this->render('_recorder') ?>
            <?php endif ?>
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
	</main>
</div>
<?= \frontend\widgets\Share::widget(['story' => $model]) ?>
