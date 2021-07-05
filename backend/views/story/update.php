<?php
use yii\helpers\Html;
/** @var $this yii\web\View */
/** @var $model common\models\Story */
/** @var $coverUploadForm backend\models\StoryCoverUploadForm */
/** @var $fileUploadForm backend\models\StoryFileUploadForm */
/** @var $powerPointForm backend\models\SourcePowerPointForm */
/** @var $wordListModel backend\models\WordListFromStoryForm */
$this->title = 'История: ' . $model->title;
$this->params['breadcrumbs'] = [
    ['label' => 'Список историй', 'url' => ['index']],
    ['label' => $model->title, 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias]), 'target' => '_blank'],
    'Изменить',
];
$this->params['sidebarMenuItems'] = [
    ['label' => $model->title, 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
    ['label' => 'Озвучка', 'url' => ['audio/index', 'story_id' => $model->id]],
];
?>
<div class="row">
    <div class="col-md-8">
    <?php if ($model->isPublished()): ?>
        <div class="alert alert-success">
            <div class="clearfix">
                <div class="pull-left" style="line-height: 34px">История опубликована <?= Yii::$app->formatter->asDate($model->published_at) ?></div>
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
                <?php if ($model->isForPublication()): ?>
                    <div class="pull-left" style="line-height: 34px">История отправлена на публикацию</div>
                    <div class="pull-right">
                        <?= Html::beginForm(['/story/cancel-publication', 'id' => $model->id]) ?>
                        <?= Html::submitButton('Отменить', ['class' => 'btn btn-primary']) ?>
                        <?= Html::endForm() ?>
                    </div>
                <?php else: ?>
                    <div class="pull-left" style="line-height: 34px">История не опубликована</div>
                    <div class="pull-right">
                        <?= Html::beginForm(['/story/publish', 'id' => $model->id]) ?>
                        <?php if ($model->submitPublicationTask()): ?>
                            <?= Html::checkbox('sendNotification', true, ['id' => 'send-notification']) . ' ' . Html::label('Запустить рассылку', 'send-notification') ?>
                        <?php endif ?>
                        <?= Html::submitButton('Опубликовать', ['class' => 'btn btn-primary']) ?>
                        <?= Html::endForm() ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    <?php endif ?>
    </div>
    <div class="col-md-4">
        <?= $this->render('_slide_actions', ['model' => $model, 'wordListModel' => $wordListModel]) ?>
    </div>
</div>
<div class="row">
	<div class="col-md-6">
		<?= $this->render('_form', [
		    'model' => $model,
		    'coverUploadForm' => $coverUploadForm,
		    'fileUploadForm' => $fileUploadForm,
		]) ?>
	</div>
	<div class="col-md-6">
		<?= $this->render('_form_powerpoint', [
            'source' => $powerPointForm,
        ]) ?>
	</div>
</div>
