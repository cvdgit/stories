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
 * @var string $fragments
 */

MainAsset::register($this);
MathAsset::register($this);

$this->registerJs("window.mathAnswers = $answers;");
$this->registerJs("window.mathFragments = $fragments;");
$this->registerJs($this->renderFile("@backend/views/test/math/_question_gaps.js"));
$this->registerCss($this->renderFile("@backend/views/test/math/_question.css"));
?>
<?php
$form = ActiveForm::begin(['id' => 'math-question-form', 'action' => $action, 'enableClientValidation' => false]) ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true, 'class' => 'form-control mathName']) ?>
<div><label for="">Текст задания</label></div>
<div id="math-wrap" style="margin-bottom: 20px">
    <div id="editor" style="min-height: 300px"><?= $formModel->job ?></div>
</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php
ActiveForm::end(); ?>
