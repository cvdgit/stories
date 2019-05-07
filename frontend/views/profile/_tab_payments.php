<?php

/** @var $payments[] common\models\Payment */

use common\helpers\PaymentHelper;

$formatter = Yii::$app->formatter;
?>
<div class="profile-tab-content payment-tab">
    <?php if (count($payments) > 0): ?>
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
                    <td><?= $formatter->asDate($payment->payment) ?></td>
                    <td><?= $formatter->asDate($payment->finish) ?></td>
                    <td><?= PaymentHelper::getStatusText($payment->state) ?></td>
                    <td><?= $formatter->asDate($payment->created_at) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Подписки не найдены</p>
    <?php endif ?>
</div>
