<?php

/* @var $this yii\web\View */
/* @var $model common\models\Story */
?>
<div class="row" style="height: 100%">
	<div class="col-xs-12" style="height: 90%">
		<h1><?= $model->title ?></h1>
		<iframe border="0" width="100%" height="100%" style="border: 0 none" src="/story/viewbyframe/<?= $model->id ?>"></iframe>
	</div>
</div>