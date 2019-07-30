<?php

use frontend\widgets\StoryFavorites;
use frontend\widgets\StoryLikeWidget;
use yii\helpers\Html;
use common\widgets\RevealWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $userCanViewStory bool */
/* @var $commentForm frontend\models\CommentForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = $model->title;
$this->setMetaTags($title,
                   $model->description,
                   'wikids, сказки, истории, просмотр истории',
                   $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

$action = Url::to(['story/init-story-player', 'id' => $model->id]);
$js = <<< JS

Wikids2.loadStory("$action");

$('#comment-form-pjax').on('pjax:success', function() {
    $.pjax.reload({container: '#comment-list-pjax'});
});

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
}
JS;
$this->registerJs($js);
?>
<div class="container story-head-container">
	<main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner" id="story-container">
                <div class="story-no-subscription"><span class="story-loader">Загрузка истории...</span></div>
            </div>
        </div>
    </main>
</div>
<div class="container">
    <main class="site-story-main">
        <div class="story-description">
            <div class="story-tags" style="margin-top: 10px; font-size: 1.4rem">
                <!--noindex-->
                <?php foreach($model->tags as $tag): ?>
                    <?= '#' . Html::a($tag->name, ['tag', 'tag' => $tag->name], ['rel' => 'nofollow']) ?>
                <?php endforeach ?>
                <!--/noindex-->
            </div>
            <h1 style="margin-top: 0; padding-top: 0;"><?= Html::encode($model->title) ?></h1>
            <div class="story-share-block">
                <?= StoryLikeWidget::widget(['storyId' => $model->id]) ?>
                <button class="btn-share" title="Поделиться" data-toggle="modal" data-target="#wikids-share-modal"><i class="glyphicon glyphicon-share"></i></button>
                <?= StoryFavorites::widget(['storyId' => $model->id]) ?>
            </div>
            <div class="story-date"><span>Опубликована:</span> <?= \common\helpers\SmartDate::dateSmart($model->created_at, true) ?></div>
            <?php if (!empty($model->description)): ?>
            <div class="story-text"><?= Html::encode($model->description) ?></div>
	  	    <?php endif ?>
	        <div class="story-categories">
                <span>Категории:</span>
                <?php foreach ($model->categories as $category): ?>
                <?= Html::a($category->name, ['story/category', 'category' => $category->alias]) ?>
                <?php endforeach ?>
            </div>
	        <div class="story-pay"><span>Тип:</span> <?= $model->bySubscription() ? 'По подписке' : 'Бесплатно' ?></div>
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
                        <h3>Смотрите также:</h3>
                        <?= \frontend\widgets\FollowingStories::widget(['storyID' => $model->id]) ?>
                    </div>
                </div>
            </div>

	    </div>
	</main>
</div>
<?= \frontend\widgets\Share::widget(['story' => $model]) ?>
