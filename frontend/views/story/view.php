<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
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
?>

<div class="vertical-slider">
	<div class="container">
		<div class="row" style="padding-top: 10px">
			<div class="col-md-12">
				<div class="reveal-container">
				    <?= RevealWidget::widget([
				    		'storyId' => $model->id,
				    		'data' => $model->body,
				    		'initScript' => '/js/story-reveal.js',
				    		'options' => [
				    			'dependencies' => [
					                ["src" => "/js/revealjs-customcontrols/customcontrols.js"],
					                ["src" => "/js/revealjs-customcontrols/customcontrols.css"],
					                ["src" => "/js/story-reveal-statistics.js"],
				    			],
				    		],
				    	]) ?>
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