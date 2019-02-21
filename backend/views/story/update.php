<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $coverUploadForm backend\models\StoryCoverUploadForm */
/* @var $fileUploadForm backend\models\StoryFileUploadForm */
/* @var $powerPointForm backend\models\StoryPowerPointForm */

$this->title = 'История: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias])];
$this->params['breadcrumbs'][] = 'Изменить';
$this->params['sidebarMenuItems'] = [
	['label' => 'Истории', 'url' => ['story/index']],
	['label' => $model->title, 'url' => ['story/update', 'id' => $model->id]],
	['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
	// ['label' => 'Изображения', 'url' => ['story/images', 'id' => $model->id]],
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
		    'powerPointForm' => $powerPointForm,
		]) ?>
	</div>
	<div class="col-xs-6" style="padding-top: 69px">
		<?php if ($model->source_id == common\models\Story::SOURCE_SLIDESCOM): ?>
		<?= $this->render('_form_dropbox', ['story' => $model, 'source' => $dropboxForm]) ?>
		<?php endif ?>
		<?php if ($model->source_id == common\models\Story::SOURCE_POWERPOINT): ?>
		<?= $this->render('_form_powerpoint', ['story' => $model, 'source' => $powerPointForm]) ?>
		<?php endif ?>
	</div>
</div>
