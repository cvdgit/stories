<?php
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
/** @var $guestStoryBody string */
\frontend\assets\LazyAsset::register($this);
$title = $model->title;
$this->setMetaTags($title,
                   $model->description,
                   $model->title . ', ' . $model->title . ' сказка на ночь',
                   $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
$actionParams = array_merge(['story/init-story-player', 'id' => $model->id], Yii::$app->request->queryParams);
$action = Url::to($actionParams);

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

$(".comment-list").on("click", ".comment-reply", function() {
    if ($(this).parent().parent().find(".comment-reply-form").children().length) {
        return;
    }
    var commentID = $(this).data("commentId");
    var form = $("#main-comment-form").clone();
    form.find("form")
        .removeClass("add-comment-focus")
        .attr("id", "reply" + commentID)
        .attr("action", "/comment/reply/" + commentID);
    $(this)
        .parent()
        .parent()
        .find(".comment-reply-form")
        .append(form)
        .find(".add-comment-placeholder textarea")
        .focus();
});

var inSlides = false;
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    var view = e.target.getAttribute("href").substr(1);
    if (view === "tab-slides") {
        inSlides = true;
        if (!isGuest) {
            WikidsStory.loadStory("$action");
        }
        window['ym'] && ym(53566996, 'reachGoal', 'transition_to_training');
    }
    else if (view === "tab-book") {
        lazy.update(false);
    }
    else {

    }
});

 $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
        $('#back-to-top').fadeIn();
    } else {
        $('#back-to-top').fadeOut();
    }
 });

$('#back-to-top')
    .click(function () {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    })
    .tooltip('show');

$(".more-facts").on("click", function() {
    $(".label:hidden", ".story-facts").show();
    $(this).hide();
})

$('.to-slides-tab').on('click', function() {
    $('#story-views-tab a:first').tab('show');
});
JS;
$this->registerJs($js);

$isSlidesView = $storyDefaultView === 'slides';
$isBookView = $storyDefaultView === 'book';

/** @var $playlist common\models\Playlist */
?>
<div class="container story-head-container">
	<main class="site-story-main">
        <?php if (Yii::$app->user->isGuest): ?>
        <?= $this->render('_story_main_block', ['model' => $model]) ?>
        <div class="tabbable-panel">
            <div class="tabbable-line">
            <?= Tabs::widget([
                'class' => 'profile-tabs',
                'id' => 'story-views-tab',
                'items' => [
                    [
                        'label' => 'Режим обучения',
                        'content' => $this->render('_tab_slides', ['model' => $model, 'playlist' => $playlist]),
                        'active' => $isSlidesView,
                        'options' => ['id' => 'tab-slides'],
                        'linkOptions' => ['class' => 'tab-slides'],
                    ],
                    [
                        'label' => 'Режим чтения',
                        'content' => $this->render('_tab_book', ['model' => $model, 'guestStoryBody' => $guestStoryBody]),
                        'active' => $isBookView,
                        'options' => ['id' => 'tab-book'],
                    ],
                ],
            ]) ?>
            </div>
        </div>
        <?php else: ?>
        <?= $this->render('_tab_slides', ['model' => $model, 'playlist' => $playlist]) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can(\common\rbac\UserRoles::ROLE_MODERATOR)): ?>
            <div class="panel panel-info" style="margin-top:10px">
                <div class="panel-body">
                    <?= $model->isPublished() ? '' : 'История не опубликована' ?>
                    <div class="pull-right">
                        <?= Html::a('Изменить', Yii::$app->urlManagerBackend->createAbsoluteUrl(['story/update', 'id' => $model->id]), ['class' => 'btn-link']) ?>
                        | <?= Html::a('Редактор', Yii::$app->urlManagerBackend->createAbsoluteUrl(['editor/edit', 'id' => $model->id]), ['class' => 'btn-link']) ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </main>
</div>

<div class="container">
    <main class="site-story-main-descr">
        <?php if (!Yii::$app->user->isGuest): ?>
        <?= $this->render('_story_main_block', ['model' => $model]) ?>
        <?php endif ?>
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
                    <div class="story-date"><span>Опубликована:</span> <?= \common\helpers\SmartDate::dateSmart($model->published_at, true) ?></div>
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
