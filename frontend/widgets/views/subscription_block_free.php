<?php

/* @var $model frontend\models\SubscriptionForm */
/* @var $rate common\models\Rate */
/* @var $hasSubscription bool */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<div class="col-md-10 col-md-offset-1 text-center">
    <div style="padding: 10px 10px 40px 10px">
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="#" class="btn btn-red" data-toggle="modal" data-target="#wikids-login-modal">Попробовать бесплатно</a>
        <?php else: ?>
            <?php
            /* if (!$hasSubscription) {
                $form = ActiveForm::begin();
                echo $form->field($model, 'subscription_id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput(['value' => $rate->id])->label(false);
                echo Html::submitButton('Попробовать бесплатно', ['class' => 'btn btn-red']);
                ActiveForm::end();
            } */
            ?>
        <?php endif ?>
        <p style="margin-top: 30px"><?= $rate->description ?></p>
    </div>
</div>
