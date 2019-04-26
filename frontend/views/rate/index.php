<?php

/** @var $this yii\web\View */
/** @var $hasSubscription bool */
/** @var $hasFreeSubscription bool */

use frontend\widgets\SubscriptionBlock;

$title = 'Подписки';
$this->setMetaTags($title, $title, $title, $title);
?>
<div class="container">
    <main class="site-pricing">
        <h1><span>Улучши возможность</span> просмотра историй</h1>
        <?php if (!$hasSubscription && !$hasFreeSubscription): ?>
        <div class="row">
            <?= SubscriptionBlock::widget(['code' => 'free', 'viewName' => 'subscription_block_free', 'hasSubscription' => $hasSubscription]) ?>
        </div>
        <?php endif ?>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="row">
                    <?= SubscriptionBlock::widget(['code' => '3months', 'image' => '/img/price-3month.png', 'hasSubscription' => $hasSubscription]) ?>
                    <?= SubscriptionBlock::widget(['code' => '1year', 'image' => '/img/price-1year.png', 'hasSubscription' => $hasSubscription]) ?>
                    <?= SubscriptionBlock::widget(['code' => '1month', 'image' => '/img/price-1month.png', 'hasSubscription' => $hasSubscription]) ?>
                </div>
            </div>
        </div>
    </main>
</div>
