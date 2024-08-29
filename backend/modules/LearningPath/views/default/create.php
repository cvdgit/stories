<?php

declare(strict_types=1);

use backend\modules\LearningPath\Create\CreateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var CreateForm $formModel
 */
?>
<?php $form = ActiveForm::begin(['id' => 'add-group-form']) ?>
<?= $form->field($formModel, 'name')->textInput() ?>
<?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
<button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
<?php ActiveForm::end() ?>
