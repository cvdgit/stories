<?php

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

$js = <<< JS
$('#comment-form-pjax').on('pjax:success', function() {
    $.pjax.reload({container: '#comment-list-pjax'});
});
JS;
$this->registerJs($js);
?>
<div class="container story-head-container">
	<main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner">
            <?= RevealWidget::widget([
                'storyId' => $model->id,
                'data' => $model->body,
                'canViewStory' => $userCanViewStory,
                'assets' => [
                    \frontend\assets\RevealAsset::class,
                    \frontend\assets\WikidsRevealAsset::class,
                ],
                'plugins' => [
                    [
                        'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
                        'buttons' => [
                            new \common\widgets\RevealButtons\FeedbackButton(),
                            new \common\widgets\RevealButtons\FullscreenButton(),
                            new \common\widgets\RevealButtons\LeftButton(),
                            new \common\widgets\RevealButtons\RightButton(),
                        ],
                    ],
                    ['class' => \common\widgets\Reveal\Plugins\Feedback::class, 'storyID' => $model->id],
                    ['class' => \common\widgets\Reveal\Plugins\Statistics::class, 'storyID' => $model->id],
                    ['class' => \common\widgets\Reveal\Plugins\Transition::class, 'storyID' => $model->id],
                ],
            ]) ?>
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
	</main>
</div>
<?= \frontend\widgets\Share::widget(['story' => $model]) ?>
