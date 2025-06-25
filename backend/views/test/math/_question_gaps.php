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
$this->registerJs($this->renderFile("@backend/views/test/math/_question_gaps.js"));
?>
<?php
$form = ActiveForm::begin(['id' => 'math-question-form', 'action' => $action, 'enableClientValidation' => false]) ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true, 'class' => 'form-control mathName']) ?>
<div style="display: flex; flex-direction: column; flex-flow: column">
    <label style="display: block; font-weight: 700; padding-top: 4px; padding-bottom: 4px;">Задание</label>
    <div style="margin: 10px 0">
        <button class="btn btn-primary btn-sm" id="add-placeholder" type="button">Вставить пропуск</button>
    </div>
    <math-field id="formula" default-mode="math" smart-mode smart-fence>
        <?= $formModel->job ?>
    </math-field>
    <label style="display: block; font-weight: 700; padding-top: 20px; padding-bottom: 4px;">Задание в LaTeX</label>
    <textarea id="latex" autocapitalize="off" autocomplete="off"
              autocorrect="off" spellcheck="false" style="display: block; color: #302e33; margin-bottom: 20px; background: #f6f7f9; min-height: 4em; height: 100%; width: calc(100% - 16px); resize: vertical; border: 1px solid #d4d4dd; outline: none; font-family: 'Berkeley Mono', 'JetBrains Mono', 'IBM Plex Mono', 'Fira Code', monospace; font-size: 16px; line-height: 1.2;"></textarea>
</div>
<div style="margin-bottom: 20px">
    <h4>Пропуски</h4>
    <div id="gaps-list" style="display: flex; flex-direction: column; row-gap: 10px; margin-bottom: 10px">
        <div style="display: flex; flex-direction: row; column-gap: 10px">
            <div style="flex: 1"><strong>Имя</strong></div>
            <div style="flex: 1"><strong>Значение</strong></div>
            <div style="width: 100px"></div>
        </div>
    </div>
    <div>
        <button id="add-gap" class="btn btn-default" type="button">Добавить пропуск</button>
    </div>
</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php
ActiveForm::end(); ?>
