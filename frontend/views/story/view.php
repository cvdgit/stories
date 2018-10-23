<?php

/* @var $this yii\web\View */
/* @var $model common\models\Story */

use frontend\widgets\RevealWidget;

?>
<div class="row" style="height: 100%">
	<div class="col-xs-12" style="height: 100%">
		<h1><?= $model->title ?></h1>
		<?= RevealWidget::widget(['data' => $model->body]) ?>
	</div>
</div>