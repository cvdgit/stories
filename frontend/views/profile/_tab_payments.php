<?php

/** @var $activePayment common\models\Payment */

use yii\helpers\Html;

?>
<div class="profile-tab-content payment-tab">
    <?php if ($activePayment === null): ?>
    <p>Преобретите <?= Html::a('подписку', ['/pricing']) ?> для просмотра всех историй</p>
    <?php else: ?>
    <p>Подписка с <?= $activePayment->payment ?> до <?= $activePayment->finish ?></p>
    <?php endif ?>
</div>
