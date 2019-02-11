<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->title . ' | wikids.ru';
$this->params['breadcrumbs'][] = ['label' => 'Каталог историй', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="vertical-slider">
	<div class="container">
		<div class="row" style="padding-top: 10px">
			<div class="col-md-12" style="height: 100%; min-height: 100%">
			<?php if ($availableRate): ?>
				<iframe border="0" width="100%" id="story-iframe" height="600" style="border: 0 none" src="/story/viewbyframe/<?= $model->id ?>" allowfullscreen></iframe>
			<?php else: ?>
				<div class="info-title">
					<p>Преобретите <?= Html::a('подписку', ['/pricing']) ?> для просмотра всех историй</p>
				</div>
			<?php endif ?>
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