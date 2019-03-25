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
@supports (--custom:property) {
  [style*="--aspect-ratio"] {
    position: relative;
  }
  [style*="--aspect-ratio"]::before {
    content: "";
    display: block;
    padding-bottom: calc(100% / (var(--aspect-ratio)));
  }  

}
CSS;
$this->registerCss($css);
?>

<div class="vertical-slider">
	<div class="container">
		<div class="row" style="padding-top: 10px">
			<div class="col-md-12">
				<div class="reveal-container">
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
				    <div style="--aspect-ratio:16/9;display:flex;align-items: center;justify-content: center;background-color:black">
				    	<?= Html::a('Смотреть по подписке', ['/pricing'], ['class' => 'custom-btn text-center']) ?>
				    </div>
				    <?php endif ?>
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