<?php

declare(strict_types=1);

use backend\VideoFromFile\Create\CreateFileForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var CreateFileForm $model
 */

$this->title = 'Новое видео из файла';
$this->params['breadcrumbs'] = [
    ['label' => 'Видео', 'url' => ['/video/file/index']],
    $this->title,
];
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'videoFile')->fileInput() ?>
            <?= $form->field($model, 'captions')->textarea(['rows' => '20']) ?>
            <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
