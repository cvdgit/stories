<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bg-grey form-container"> 
<div class="container">
    <?= Alert::widget() ?>
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
                <?= $form->field($model, 'password', ['inputOptions' => ['placeholder' => 'Пароль']])->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>   
                <div class="info-text">
                    Если вы забыли свой пароль, вы можете <?= Html::a('сбросить его', ['site/request-password-reset']) ?>.
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Войти', ['class' => 'custom-btn white form-btn', 'name' => 'login-button']) ?>  
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>