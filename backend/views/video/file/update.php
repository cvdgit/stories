<?php

declare(strict_types=1);

use backend\VideoFromFile\Update\UpdateFileForm;
use frontend\assets\PlyrAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var UpdateFileForm  $model
 */

PlyrAsset::register($this);

$this->title = 'Изменить видео';
$this->params['breadcrumbs'] = [
    ['label' => 'Видео', 'url' => ['/video/file/index']],
    $this->title,
];
?>
<div class="row">
    <div class="col-md-6">
        <h1><?= Html::encode($this->title); ?></h1>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'title')->textInput(); ?>
        <?= $form->field($model, 'captions')->textarea(['rows' => '20']); ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">
        <div style="margin-top: 72px">
            <video id="player" playsinline controls>
                <source src="<?= $model->getVideoUrl(); ?>" type="video/mp4" />
                <?php if ($model->isHaveCaptions()): ?>
                <track
                    kind="captions"
                    src="<?= Url::to(['/video/file/captions', 'id' => $model->getId()]); ?>"
                    srclang="en"
                    label="English"
                    default
                >
                <?php endif; ?>
            </video>
        </div>
    </div>
</div>
<?php
$js = <<< JS
var player = new Plyr('#player', {
    captions: {
        active: true,
        language: 'en',
        update: true
    }
});
JS;
$this->registerJs($js);
