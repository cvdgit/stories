<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>

<div class="col-sm-6 col-md-4">
<div class="thumbnail">
	<?php if (empty($model->cover)): ?>
	<img src="holder.js/240x200/" alt="...">
	<?php else: ?>
	<img src="<?= $model->getCoverPath() ?>" alt="...">
	<?php endif ?>
	<div class="caption">
		<h3><?= Html::encode($model->title) ?></h3>
		<p>Краткое описание истории</p>
		<p><?= Html::a('Перейти к истории »', ['view', 'alias' => $model->alias], ['class' => 'btn btn-default']) ?></p>
	</div>
</div>
</div>