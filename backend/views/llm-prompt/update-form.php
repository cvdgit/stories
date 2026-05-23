<?php

declare(strict_types=1);

use backend\LlmPrompt\UpdatePromptForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var UpdatePromptForm $formModel
 */

$this->title = 'Изменить промт';
?>
<div class="row">
    <div class="col-xs-6">
        <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
        <div class="news-add">
            <?php $form = ActiveForm::begin() ?>
            <?= $form->field($formModel, 'name')->textInput(['maxlength' => 255]) ?>
            <?= $form->field($formModel, 'key')->textInput(['maxlength' => 255]) ?>
            <?= $form->field($formModel, 'prompt')->textarea(['rows' => 20]) ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
