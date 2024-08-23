<?php

declare(strict_types=1);

use frontend\ConsultRequest\ConsultRequestForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var ConsultRequestForm $formModel
 */
?>
<div class="modal request-dialog" id="consult-request-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h5 class="modal-title">Оставить<br><span>заявку</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
            $form = ActiveForm::begin([
                'action' => ['/consult/request'],
                'id' => 'consult-request-form',
                'options' => ['class' => 'contact-form'],
            ]); ?>
            <div class="modal-body">
                <div class="contact-form__controls">
                    <?= $form->field($formModel, 'name')->textInput(
                        ['maxlength' => true, 'placeholder' => 'Ваше имя', 'autocomplete' => 'name'],
                    )->label(false); ?>
                    <?= $form->field($formModel, 'email')->textInput(
                        ['type' => 'email', 'maxlength' => true, 'placeholder' => 'Email'],
                    )->label(false) ?>
                    <?= $form->field($formModel, 'phone')->textInput(
                        ['type' => 'tel', 'maxlength' => true, 'placeholder' => 'Номер телефона'],
                    )->label(false) ?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="contact-form-submit__wrap">
                    <button type="submit" class="button">Отправить</button>
                </div>
                <div class="discount-agree">
                    <p class="discount-agree__text">
                        Нажимая на кнопку вы принимаете
                        <br>
                        <a href="#" class="discount-agree__link">пользовательское соглашение</a>
                    </p>
                </div>
            </div>
            <?php
            ActiveForm::end(); ?>
        </div>
    </div>
</div>
