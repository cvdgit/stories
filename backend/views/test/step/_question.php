<?php

declare(strict_types=1);

use backend\assets\MainAsset;
use backend\assets\MathAsset;
use backend\Testing\Questions\Step\Create\StepQuestionCreateForm;
use backend\Testing\Questions\Step\Update\StepQuestionUpdateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var StepQuestionCreateForm|StepQuestionUpdateForm $formModel
 * @var bool $isNewRecord
 * @var string $action
 * @var string $steps
 */

MainAsset::register($this);
MathAsset::register($this);

$this->registerJs("window.steps = $steps;");
$this->registerJs($this->renderFile("@backend/views/test/step/_step.js"));
$this->registerCss($this->renderFile("@backend/views/test/step/_step.css"));
?>
<?php
$form = ActiveForm::begin(['id' => 'step-question-form', 'action' => $action, 'enableClientValidation' => false]) ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true, 'class' => 'form-control stepQuestionName']) ?>
<div>
    <div>
        <div><label for="">Текст задания</label></div>
        <div id="math-wrap" style="margin-bottom: 20px">
            <div id="editor" style="min-height: 100px"><?= $formModel->job ?></div>
        </div>
    </div>
    <h3 class="h4">Этапы:</h3>
    <div id="step-list"></div>
    <div style="margin: 20px 0">
        <button id="add-step" type="button" class="btn">Добавить этап</button>
    </div>
</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php
ActiveForm::end(); ?>
