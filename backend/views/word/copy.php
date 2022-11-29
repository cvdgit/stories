<?php

declare(strict_types=1);

use backend\forms\WordForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var WordForm $model
 */
?>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'options' => [
        'id' => 'copy-test-word-form'
    ],
]); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'correct_answer')->textInput(['maxlength' => true]); ?>
<?= Html::submitButton('Создать копию', ['class' => 'btn btn-success']); ?>
<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
<?php ActiveForm::end(); ?>
