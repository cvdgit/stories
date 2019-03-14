<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$title = 'Регистрация';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
$this->params['breadcrumbs'][] = $title;
?>
<div class="form-container">
    <div class="container">
        <?= Alert::widget() ?>
        <div class="site-signup">
            <div class="row widget-search">
                <div class="col-lg-6">
                    <?php $form = ActiveForm::begin(['id' => 'login-form',
                        'fieldConfig' => [
                            'template' => '<div class="el-container">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'widget-title'],
                            'inputOptions' => ['class'=> null]
                        ]
                    ]); ?>
                        <?= $form->field($model, 'username', ['inputOptions' => ['placeholder' => 'Имя пользователя']])->textInput(['autofocus' => true]) ?>
                        <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email пользователя']]) ?>
                        <?= $form->field($model, 'password', ['inputOptions' => ['placeholder' => 'Пароль']])->passwordInput() ?>    
                        <p class="info-text" align="justify">Указывая свои данные, вы даете полное согласие на обработку персональных данных в соответствии с <?= Html::a('политикой конфиденциальности', ['/policy']) ?>.</p>
                        <?= Html::submitButton('Зарегистрироваться', ['class' => 'custom-btn form-btn', 'name' => 'signup-button']) ?>    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>