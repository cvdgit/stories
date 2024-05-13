<?php

declare(strict_types=1);

use frontend\assets\GameAsset;
use frontend\Game\Deploy\DeployForm;
use yii\bootstrap\Html;
use yii\web\View;
use yii\bootstrap\ActiveForm;

/**
 * @var View $this
 * @var DeployForm $formModel
 */

GameAsset::register($this);

$this->title = 'Game deploy';
?>
<div style="display: flex; justify-content: center; align-items: center; height: 100vh">
    <div style="margin-bottom: 200px; min-width: 400px">
        <h1 class="h2" style="margin-bottom: 20px">Загрузка сборки</h1>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($formModel, 'buildName')->textInput(['readonly' => true]); ?>
        <?= $form->field($formModel, 'zipFile')->fileInput(['accept' => 'zip,application/zip,application/x-zip,application/x-zip-compressed']) ?>
        <div style="margin-top: 20px; text-align: center">
            <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
