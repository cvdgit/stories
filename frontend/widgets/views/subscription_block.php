<?php

/* @var $model frontend\models\SubscriptionForm */
/* @var $rate common\models\Rate */
/* @var $image string */
/* @var $hasSubscription bool */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div class="col-xs-8 col-xs-offset-2 col-sm-4 col-sm-offset-0 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0">
    <div class="price">
        <div class="price-image"><img src="<?= $image ?>" alt=""></div>
        <div class="price-name"><?= $rate->title ?></div>
        <div class="price-description"><?= $rate->description ?></div>
        <div class="price-amount"><?= $rate->cost ?> ₽</div>
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="#wikids-login-modal" class="btn" data-toggle="modal">Купить</a>
        <?php else: ?>
            <?php
            /* if (!$hasSubscription) {
                $form = ActiveForm::begin(['action' => ['/payment/create'], 'options' => ['class' => 'payment-form']]);
                echo $form->field($model, 'subscription_id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput(['value' => $rate->id])->label(false);
                echo Html::submitButton('Купить', ['class' => 'btn']);
                ActiveForm::end();
            } */
            ?>
        <?php endif ?>
    </div>
</div>
