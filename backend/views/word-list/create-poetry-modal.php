<?php

declare(strict_types=1);

use backend\forms\WordListPoetryForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var WordListPoetryForm $formModel
 */
?>

<?php $form = ActiveForm::begin(['id' => 'poetry-form']); ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($formModel, 'line_per_question')->textInput() ?>
<?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
<?php ActiveForm::end(); ?>
