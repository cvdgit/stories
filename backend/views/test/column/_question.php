<?php

declare(strict_types=1);

use backend\assets\MainAsset;
use backend\Testing\Questions\Column\Create\ColumnQuestionCreateForm;
use backend\Testing\Questions\Column\Update\ColumnQuestionUpdateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var ColumnQuestionCreateForm|ColumnQuestionUpdateForm $formModel
 * @var bool $isNewRecord
 * @var string $action
 */

MainAsset::register($this);
$this->registerJs($this->renderFile("@backend/views/test/column/_column.js"));
?>
<?php
$form = ActiveForm::begin([
    'id' => 'column-question-form',
    'action' => $action,
    'enableClientValidation' => false,
    'options' => [
        'data-model-name' => array_reverse(explode('\\', get_class($formModel)))[0],
    ],
]) ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true, 'class' => 'form-control columnQuestionName']) ?>
<div style="display: flex; flex-direction: row; margin-bottom: 20px; column-gap: 10px; align-items: center">
    <div>
        <?= $form->field($formModel, 'firstDigit')->textInput(['class' => 'form-control firstDigit']) ?>
    </div>
    <div>
        <?= $form->field($formModel, 'sign')->dropDownList(['+' => '+', '-' => '-', '*' => '*'], ['class' => 'form-control sign']) ?>
    </div>
    <div>
        <?= $form->field($formModel, 'secondDigit')->textInput(['class' => 'form-control secondDigit']) ?>
    </div>
    <div>
        <div>=</div>
    </div>
    <div>
        <?= $form->field($formModel, 'result')->textInput(['class' => 'form-control result', 'readonly' => true]) ?>
    </div>
</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php
ActiveForm::end(); ?>
