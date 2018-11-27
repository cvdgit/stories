<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$this->title = 'Запросить сброс пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bg-grey form-container">
    <div class="container">
        <?= Alert::widget() ?>
        <div class="site-request-password-reset">
        <div class="info-text">Пожалуйста, заполните свой адрес электронной почты. Будет отправлена ссылка на сброс пароля.</div>
            <div class="row widget-search">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form',
                        'fieldConfig' => [
                            'template' => '<div class="el-container">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'widget-title'],
                            'inputOptions' => ['class'=> null]
                        ]
                    ]); ?>                        
                        <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email пользователя']])->textInput(['autofocus' => true]) ?>    
                        <?= Html::submitButton('Отправить', ['class' => 'custom-btn white form-btn']) ?>    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>