<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $coverUploadForm backend\models\StoryCoverUploadForm */

$this->title = 'Создание истории';
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-xs-6">
		<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
		<?= $this->render('_form', [
		    'model' => $model,
		    'coverUploadForm' => $coverUploadForm,
		]) ?>
	</div>
	<div class="col-xs-6"></div>
</div>
