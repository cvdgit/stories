<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $coverUploadForm backend\models\StoryCoverUploadForm */
/* @var $fileUploadForm backend\models\StoryFileUploadForm */

$this->title = 'История: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias])];
$this->params['breadcrumbs'][] = 'Изменить';

$this->params['sidebarMenuItems'] = [
	['label' => $model->title, 'url' => ['story/update', 'id' => $model->id]],
	['label' => 'Изображения', 'url' => ['story/images', 'id' => $model->id]],
];
?>
<div class="row">
	<div class="col-xs-6">
		<div id="alert_placeholder"></div>
		<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
		<?= $this->render('_form', [
		    'model' => $model,
		    'coverUploadForm' => $coverUploadForm,
		    'fileUploadForm' => $fileUploadForm,
		]) ?>
	</div>
	<div class="col-xs-6"></div>
</div>
