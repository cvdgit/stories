<?php

declare(strict_types=1);

use backend\Testing\Questions\Column\Import\ImportColumnQuestionsForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var int $testId
 * @var ImportColumnQuestionsForm $formModel
 */
?>
<?php $form = ActiveForm::begin(['action' => ['/test/column/import', 'test_id' => $testId], 'id' => 'column-questions-import-form']) ?>
<?= $form->field($formModel, 'sign')->checkboxList(['+' => '+', '-' => '-', '*' => '*']) ?>
<div style="display: grid; grid-template-columns: 1fr 1fr; grid-column-gap: 20px">
    <div><?= $form->field($formModel, 'firstDigitMin')->textInput(['maxlength' => true]) ?></div>
    <div><?= $form->field($formModel, 'firstDigitMax')->textInput(['maxlength' => true]) ?></div>
</div>
<div style="display: grid; grid-template-columns: 1fr 1fr; grid-column-gap: 20px">
    <div><?= $form->field($formModel, 'secondDigitMin')->textInput(['maxlength' => true]) ?></div>
    <div><?= $form->field($formModel, 'secondDigitMax')->textInput(['maxlength' => true]) ?></div>
</div>
<?= $form->field($formModel, 'number')->dropDownList([10 => '10', 20 => '20', 30 => '30'], ['prompt' => 'Количество вопросов']) ?>
<div class="modal-footer">
    <button type="submit" class="btn btn-success">Импортировать</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end() ?>
