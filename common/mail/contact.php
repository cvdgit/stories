<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form frontend\models\ContactForm */
?>
<div class="contact-form">
    <p><?= Html::encode($form->email) ?></p>
    <p><?= Html::encode($form->name) ?></p>
    <p><?= Html::encode($form->subject) ?></p>
    <p><?= Html::encode($form->body) ?></p>
</div>