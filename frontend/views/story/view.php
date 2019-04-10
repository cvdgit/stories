<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\JsExpression;
use common\widgets\RevealWidget;
use common\components\StoryCover;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$title = $model->title;
$this->setMetaTags($title,
                   $model->description,
                   'wikids, сказки, истории, просмотр истории',
                   $title);
$this->params['breadcrumbs'][] = ['label' => 'Каталог историй', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;

$js = <<< JS
function onSlideMouseDown(e) {
	e = e || window.event;
	if ($(e.target).parents('.story-controls').length) return;
	switch (e.which) {
		case 1: Reveal.next(); break;
		// case 2: alert('middle'); break;
		case 3: Reveal.prev(); break; 
	}
}
Reveal.addEventListener("mousedown", onSlideMouseDown, false);
JS;
$this->registerJs($js);
?>
<div class="container">
	<main class="site-story-main">
	  <div class="story-container">
	    <div class="story-container-inner">
			<?php if ($userCanViewStory): ?>
		    <?= RevealWidget::widget([
	    		'storyId' => $model->id,
	    		'data' => $model->body,
	    		'options' => [
	    			'dependencies' => [
	            ["src" => "/js/revealjs-customcontrols/customcontrols.js"],
	            ["src" => "/js/story-reveal-statistics.js"],
	    			],
	    		],
	    		'controls' => [
	    			new \common\widgets\RevealButtons\FeedbackButton(),
	    			new \common\widgets\RevealButtons\FullscreenButton(),
	    			new \common\widgets\RevealButtons\LeftButton(),
	    			new \common\widgets\RevealButtons\RightButton(),
					],
	    		'controlsCallback' => new JsExpression("
	function(ev) {
	var left = $('.custom-navigate-left', $('.reveal'));
	Reveal.getProgress() === 0 ? left.attr('disabled', 'disabled') : left.removeAttr('disabled');
	var right = $('.custom-navigate-right', $('.reveal'));
	Reveal.getProgress() === 1 ? right.attr('disabled', 'disabled') : right.removeAttr('disabled');
	}
					"),
		    ]) ?>
		  <?php else: ?>
		    <div class="story-no-subscription">
		    	<?= Html::a('Смотреть по подписке', ['/pricing'], ['class' => 'btn']) ?>
		    </div>
		  <?php endif ?>
	    </div>
	  </div>
	  <div class="story-description">
	    <h1><?= Html::encode($model->title) ?></h1>
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
	    <div class="story-pay">Тип: <?= $model->bySubscription() ? 'По подписке' : 'Беслпатно' ?></div>
	  </div>
	  <div class="comments">
	  	<?php if (!Yii::$app->user->isGuest): ?>
	    <div class="comment-form">
	      <div class="comment-form-wrapper">
	        <div class="comment-logo">
	          <img src="/img/avatar.png" alt="">
	        </div>
	        <div class="add-comment-wrapper">
	          <div class="add-comment-placeholder">
	            <textarea placeholder="Оставить комментарий..."></textarea>
	          </div>
	          <div class="add-comment-controls">
	            <a class="add-comment-close" href="#!">Отмена</a>
	            <button class="btn">Оставить комментарий</button>
	          </div>
	        </div>
	      </div>
	    </div>
		<?php endif ?>
	    <div class="comment-list">
	    	<h4>Комменатриев пока нет</h4>
	    </div>
	  </div>
	</main>
</div>