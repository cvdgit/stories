<?php

/* @var $model frontend\models\SubscriptionForm */
/* @var $rate common\models\Rate */
/* @var $image string */
/* @var $hasSubscription bool */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div class="col-md-4 col-sm-4">
    <div class="price">
        <div class="price-image"><img src="<?= $image ?>" alt=""></div>
        <div class="price-name"><?= $rate->title ?></div>
        <div class="price-description"><?= $rate->description ?></div>
        <div class="price-amount"><?= $rate->cost ?> ₽</div>
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="#" class="btn" data-toggle="modal" data-target="#wikids-login-modal">Купить</a>
        <?php else: ?>
            <?php if (!$hasSubscription): ?>
                <?php $form = ActiveForm::begin(['action' => ['/payment/create'], 'options' => ['class' => 'payment-form']]); ?>
                <?= $form->field($model, 'subscription_id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput(['value' => $rate->id])->label(false) ?>
                <?= Html::submitButton('Купить', ['class' => 'btn']) ?>
                <?php ActiveForm::end(); ?>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
