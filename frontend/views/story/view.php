<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\JsExpression;
use common\widgets\RevealWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$title = $model->title;
$this->setMetaTags($title,
                   $model->description,
                   'wikids, сказки, истории, просмотр истории',
                   $title);
$this->params['breadcrumbs'][] = ['label' => 'Каталог историй', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
$css = <<< CSS
.reveal-container {
	position: relative;
}
.reveal-container::before {
    content: "";
    display: block;
    padding-bottom: calc(100% / (16/9));
    width: 100%;
}
.reveal-container-inner {
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
}
.reveal-no-subscription {
	display: flex;
	align-items: center;
	justify-content: center;
	background-color: black;
	height: 100%;
}
CSS;
$this->registerCss($css);
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
<div class="vertical-slider">
	<div class="container">
		<div class="row" style="padding-top: 10px">
			<div class="col-md-12">
				<div class="reveal-container">
					<div class="reveal-container-inner">
					<?php if ($userCanViewStory): ?>
				    <?= RevealWidget::widget([
				    		'storyId' => $model->id,
				    		'data' => $model->body,
				    		'options' => [
				    			'dependencies' => [
					                ["src" => "/js/revealjs-customcontrols/customcontrols.js"],
					                ["src" => "/js/revealjs-customcontrols/customcontrols.css"],
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
				    <div class="reveal-no-subscription">
				    	<?= Html::a('Смотреть по подписке', ['/pricing'], ['class' => 'custom-btn text-center']) ?>
				    </div>
				    <?php endif ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="inside-single" style="padding-left:0">
					<h4 class="title"><?= Html::encode($model->title) ?></h4>
					<div class="description">
						<?php if (!empty($model->description)): ?>
						<p><?= Html::encode($model->description) ?></p>
						<?php endif ?>
					</div>
					<ul>
						<li>Категории: <?= Html::a($model->category->name, ['story/category', 'category' => $model->category->alias]) ?></li>
						<?php $tags = $model->getTags()->all(); ?>
						<?php if (count($tags) > 0): ?>
						<li>Тэги:
						<?php foreach($tags as $tag): ?>
							<?= Html::a($tag->name, ['tag', 'tag' => $tag->name]) ?>
						<?php endforeach ?>
						</li>
						<?php endif ?>
						<li>Тип: <a href="#"><?= ($model->sub_access) ? 'Подписка' : 'Бесплатно' ?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>