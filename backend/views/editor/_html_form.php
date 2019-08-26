<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model backend\models\editor\QuestionForm */

$form = ActiveForm::begin([
    'action' => ['/editor/update-question'],
    'id' => 'block-form',
]);
?>
    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'width') ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'top') ?></div>
    </div>
    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'height') ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'left') ?></div>
    </div>
<?php
echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo $form->field($model, 'block_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
