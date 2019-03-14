<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use common\widgets\Alert;

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-container">
    <div class="container">
        <?= Alert::widget() ?>
        <div class="site-contact">
            <div class="info-text">
                Если у вас есть деловые вопросы или другие вопросы, пожалуйста, заполните следующую форму, чтобы связаться с нами. Спасибо.
            </div>
            <div class="row widget-search">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin(['id' => 'contact-form',
                        'fieldConfig' => [
                            'template' => '<div class="el-container">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'widget-title'],
                            'inputOptions' => ['class'=> null]
                        ]
                    ]); ?>
                        <?= $form->field($model, 'name', ['inputOptions' => ['placeholder' => 'Имя пользователя']])->textInput(['autofocus' => true]) ?>
                        <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'Email пользователя']]) ?>
                        <?= $form->field($model, 'subject', ['inputOptions' => ['placeholder' => 'Тема']]) ?>
                        <?= $form->field($model, 'body', ['inputOptions' => ['placeholder' => 'Сообщение']])->textarea(['rows' => 6]) ?>
                        <?= $form->field($model, 'verifyCode', ['inputOptions' => ['class'=> null, 'placeholder' => 'Код подтверждения']])->widget(Captcha::className(), [
                            'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-9">{input}</div></div>',
                            'options' => ['class'=> null, 'placeholder' => 'Код подтверждения'],
                        ]) ?>                         
                        <p class="info-text" align="justify">Указывая свои данные, вы даете полное согласие на обработку персональных данных в соответствии с <?= Html::a('политикой конфиденциальности', ['/policy']) ?>.</p> 
                        <?= Html::submitButton('Отправить', ['class' => 'custom-btn form-btn']) ?>    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>