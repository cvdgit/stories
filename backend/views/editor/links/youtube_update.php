<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model backend\models\links\UpdateYoutubeLink */
$this->title = 'Ссылка на видео YouTube';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Ссылки', 'url' => ['index', 'slide_id' => $model->slide_id]],
];
?>
<div>
    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php $form = ActiveForm::begin([
                'action' => ['youtube-update', 'id' => $model->getModelID()],
            ]); ?>
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'youtube_id')->textInput() ?>
            <?= $form->field($model, 'start')->textInput() ?>
            <?= $form->field($model, 'end')->textInput() ?>
            <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
