<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProfileEditForm */
/* @var $form ActiveForm */

$title = 'Редактировать профиль';
$this->setMetaTags($title, $title, '', $title);
?>
<h1>Редактировать <span>профиль</span></h1>
<div class="row">
    <div class="col-md-6 col-md-offset-1">
        <div class="text-center">
            <?php $form = ActiveForm::begin(['options' => [
                'class' => 'story-form',
            ]]); ?>
            <?= $form->field($model, 'first_name')->textInput(['placeholder' => 'Имя']) ?>
            <?= $form->field($model, 'last_name')->textInput(['placeholder' => 'Фамилия']) ?>
            <?= $form->field($model->photoForm, 'file')->fileInput(['accept' => 'image/*']) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-small']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>