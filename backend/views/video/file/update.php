<?php
use frontend\assets\PlyrAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model backend\models\video\UpdateFileVideoForm */
PlyrAsset::register($this);
$this->title = 'Изменить видео';
$this->params['breadcrumbs'] = [
    ['label' => 'Видео', 'url' => ['video/index', 'source' => $model->source]],
    $this->title,
];
?>
<div class="row">
    <div class="col-md-6">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'title')->textInput() ?>
        <?= $form->field($model, 'videoFile')->fileInput() ?>
        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">
        <div style="margin-top: 72px">
            <video id="player" playsinline controls>
                <source src="<?= $model->getVideoUrl() ?>" type="video/mp4" />
            </video>
        </div>
    </div>
</div>
<?php
$js = <<< JS
var player = new Plyr('#player');
JS;
$this->registerJs($js);
