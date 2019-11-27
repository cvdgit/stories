<?php

use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $model common\models\Story */
/** @var $coverUploadForm backend\models\StoryCoverUploadForm */
/** @var $fileUploadForm backend\models\StoryFileUploadForm */
/** @var $powerPointForm backend\models\SourcePowerPointForm */
/** @var $audioUploadForm backend\models\AudioUploadForm */

$this->title = 'История: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias])];
$this->params['breadcrumbs'][] = 'Изменить';
$this->params['sidebarMenuItems'] = [
	['label' => 'Истории', 'url' => ['story/index']],
	['label' => $model->title, 'url' => ['story/update', 'id' => $model->id]],
	['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
	['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
    ['label' => 'Озвучка', 'url' => ['audio/index', 'story_id' => $model->id]],
];
?>
<div class="row">
	<div class="col-xs-6">
        <?php if ($model->isPublished()): ?>
        <div class="alert alert-success">
            <div class="clearfix">
                <div class="pull-left" style="line-height: 34px">История опубликована</div>
                <div class="pull-right">
                    <?= Html::beginForm(['/story/unpublish', 'id' => $model->id]) . Html::submitButton('Снять с публикации', ['class' => 'btn btn-primary']) . Html::endForm() ?>
                </div>
            </div>
        </div>
        <?php if ($model->isOriginalAudioTrack()): ?>
            <?php
            $published = $model->audioTrackPublished();
            echo $this->render('_publication', [
                'published' => $published,
                'text' => 'Озвучка ' . ($published ? 'опубликована' : 'не опубликована'),
                'action' => $published ? ['audio/unpublish', 'story_id' => $model->id] : ['audio/publish', 'story_id' => $model->id],
            ]);
            ?>
        <?php endif ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <div class="clearfix">
                    <div class="pull-left" style="line-height: 34px">История не опубликована</div>
                    <div class="pull-right">
                        <?= Html::beginForm(['/story/publish', 'id' => $model->id]) . Html::submitButton('Опубликовать', ['class' => 'btn btn-primary']) . Html::endForm() ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
		<?= $this->render('_form', [
		    'model' => $model,
		    'coverUploadForm' => $coverUploadForm,
		    'fileUploadForm' => $fileUploadForm,
		]) ?>
	</div>
	<div class="col-xs-6" style="padding-top: 69px">
		<?= $this->render('_form_powerpoint', ['story' => $model, 'source' => $powerPointForm]) ?>
	</div>
</div>
