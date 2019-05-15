<?php

use yii\helpers\Html;
use common\widgets\RevealWidget;

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
$this->params['breadcrumbs'][] = ['label' => 'Каталог историй', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;

$js = <<< JS
$('#comment-form-pjax').on('pjax:success', function() {
    $.pjax.reload({container: '#comment-list-pjax'});
});
JS;
$this->registerJs($js);
?>
<div class="container">
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
                ],
            ]) ?>
            </div>
        </div>
        <div class="story-description">
            <h1><?= Html::encode($model->title) ?>                <div class="story-share-block">
                    <button class="btn" data-toggle="modal" data-target="#wikids-share-modal">Поделиться</button>
                </div></h1>
            <?php if (!empty($model->description)): ?>
            <div class="story-text"><?= Html::encode($model->description) ?></div>
	  	    <?php endif ?>
	        <div class="story-categories">Категория: <?= Html::a($model->category->name, ['story/category', 'category' => $model->category->alias]) ?></div>
	        <?php $tags = $model->getTags()->all(); ?>
			<?php if (count($tags) > 0): ?>
	        <div class="story-tags">Тэги:
	    	<?php foreach($tags as $tag): ?>
				<?= Html::a($tag->name, ['tag', 'tag' => $tag->name]) ?>
            <?php endforeach ?>
	        </div>
	        <?php endif ?>
	        <div class="story-pay">Тип: <?= $model->bySubscription() ? 'По подписке' : 'Бесплатно' ?></div>
	    </div>
	    <div class="comments">
	  	<?php if (!Yii::$app->user->isGuest): ?>
            <?= $this->render('_comment_form', ['commentForm' => $commentForm]) ?>
        <?php endif ?>
	        <div class="comment-list">
                <?= $this->render('_comment_list', ['dataProvider' => $dataProvider]) ?>
	        </div>
	    </div>
	</main>
</div>

<?= \frontend\widgets\Share::widget(['story' => $model]) ?>

