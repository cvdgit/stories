<?php

/** @var $activePayment common\models\Payment */

use yii\helpers\Html;

?>
<div class="profile-tab-content payment-tab">
    <?php if ($activePayment === null): ?>
    <p>Преобретите <?= Html::a('подписку', ['/pricing']) ?> для просмотра всех историй</p>
    <?php else: ?>
        <p>Активная подписка: <strong><?= $activePayment->rate->title ?></strong></p>
        <p>Период действия с <strong><?= Yii::$app->formatter->asDate($activePayment->payment) ?></strong> по <strong><?= Yii::$app->formatter->asDate($activePayment->finish) ?></strong></p>
    <?php endif ?>
</div>
