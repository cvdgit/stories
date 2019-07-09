<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProfileEditForm */
/* @var $form ActiveForm */

$title = 'Редактировать профиль';
$this->setMetaTags($title,
    $title,
    '',
    $title);
?>
<div class="container">
    <main class="site-user-profile">
        <h1><span>Редактировать</span> профиль</h1>
        <div class="site-request-password-reset">
            <div class="row">
                <div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                    <?php $form = ActiveForm::begin(['options' => [
                        'class' => 'story-form',
                    ]]); ?>
                    <?= $form->field($model, 'first_name')->textInput(['placeholder' => 'Имя']) ?>
                    <?= $form->field($model, 'last_name')->textInput(['placeholder' => 'Фамилия']) ?>
                    <?= $form->field($model->photoForm, 'file')->fileInput(['accept' => 'image/*']) ?>
                    <?= Html::submitButton('Сохранить', ['class' => 'btn']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </main>
</div>
