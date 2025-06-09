<?php

declare(strict_types=1);

use backend\assets\MainAsset;
use backend\assets\MathAsset;
use backend\Testing\Questions\Math\Create\MathQuestionCreateForm;
use backend\Testing\Questions\Math\Update\MathQuestionUpdateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var MathQuestionCreateForm|MathQuestionUpdateForm $formModel
 * @var bool $isNewRecord
 * @var string $action
 * @var string $answers
 */

MainAsset::register($this);
MathAsset::register($this);

$this->registerJs("window.mathAnswers = $answers;");
$this->registerJs($this->renderFile("@backend/views/test/math/_question.js"));
//$this->registerCss($this->renderFile("@backend/views/test/math/_question.css"));
?>
<?php
$form = ActiveForm::begin(['id' => 'math-question-form', 'action' => $action]) ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true, 'class' => 'form-control mathName']) ?>
<div style="display: flex; flex-direction: column; flex-flow: column">
    <label style="display: block; font-weight: 700; padding-top: 4px; padding-bottom: 4px;">Задание</label>
    <math-field id="formula">
        <?= $formModel->job ?>
    </math-field>
    <label style="display: block; font-weight: 700; padding-top: 20px; padding-bottom: 4px;">Задание в LaTeX</label>
    <textarea id="latex" autocapitalize="off" autocomplete="off"
              autocorrect="off" spellcheck="false" style="display: block; color: #302e33; margin-bottom: 20px; background: #f6f7f9; min-height: 4em; height: 100%; width: calc(100% - 16px); resize: vertical; border: 1px solid #d4d4dd; outline: none; font-family: 'Berkeley Mono', 'JetBrains Mono', 'IBM Plex Mono', 'Fira Code', monospace; font-size: 16px; line-height: 1.2;"></textarea>
    <?= $form->field($formModel, 'haveJob')->hiddenInput(['class' => 'form-control mathJob'])->label(false) ?>
</div>
<div style="margin-bottom: 20px">
    <h4>Варианты ответов:</h4>
    <div id="answer-list" style="display: flex; flex-direction: column; row-gap: 10px; margin-bottom: 10px"></div>
    <?= $form->field($formModel, 'haveAnswers')->hiddenInput(['class' => 'form-control mathAnswers'])->label(false) ?>
    <div>
        <button id="add-answer" class="btn btn-default" type="button">Добавить вариант ответа</button>
    </div>
</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php
ActiveForm::end(); ?>
