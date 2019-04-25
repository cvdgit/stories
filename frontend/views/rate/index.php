<?php

/* @var $this yii\web\View */
/* @var $rates[] common\models\Rate */
/* @var $model common\models\SubscriptionModel */
/* @var $hasSubscription bool */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$title = 'Подписки';
$this->setMetaTags($title,
                   $title,
                   $title,
                   $title);
?>
<div class="container">
    <main class="site-pricing">
        <h1><span>Улучши возможность</span> просмотра историй</h1>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 text-center">
                <div style="padding: 10px 10px 40px 10px">
                    <button class="btn btn-red">Попробовать бесплатно</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="row">
                <?php $images = ['/img/price-3month.png', '/img/price-1year.png', '/img/price-1month.png']; ?>
                <?php foreach ($rates as $i => $rate): ?>
                    <div class="col-md-4 col-sm-4">
                        <div class="price">
                            <div class="price-image"><img src="<?= $images[$i] ?>" alt=""></div>
                            <div class="price-name"><?= $rate->title ?></div>
                            <div class="price-description"><?= $rate->description ?></div>
                            <div class="price-amount"><?= $rate->cost ?> ₽</div>
                            <?php if (Yii::$app->user->isGuest): ?>
                                <a href="#" class="btn" data-toggle="modal" data-target="#wikids-login-modal">Купить</a>
                            <?php else: ?>
                            <?php if (!$hasSubscription): ?>
                            <?php $form = ActiveForm::begin(); ?>
                            <?= $form->field($model, 'subscription_id', ['template' => '{input}', 'options' => ['tag' => false]])->hiddenInput(['value' => $rate->id])->label(false) ?>
                            <?= Html::submitButton('Купить', ['class' => 'btn']) ?>
                            <?php ActiveForm::end(); ?>
                            <?php endif ?>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
            </div>
        </div>
    </main>
</div>
