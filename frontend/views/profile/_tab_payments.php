<?php

/** @var $payments[] common\models\Payment */

use common\helpers\PaymentHelper;
use yii\helpers\Html;

?>
<div class="profile-tab-content payment-tab">
    <?php if ($payments !== null): ?>
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Вид подписки</th>
                <th>Подписка с</th>
                <th>Подписка по</th>
                <th>Статус</th>
                <th>Дата создания</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= $payment->rate->title ?></td>
                    <td><?= Yii::$app->formatter->asDate($payment->payment) ?></td>
                    <td><?= Yii::$app->formatter->asDate($payment->finish) ?></td>
                    <td><?= PaymentHelper::getStatusText($payment->state) ?></td>
                    <td><?= Yii::$app->formatter->asDate($payment->created_at) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php else: ?>
    <?php endif ?>
</div>
