<?php

use common\helpers\PaymentHelper;
use common\models\News;
use common\models\Payment;
use yii\bootstrap\Nav;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $this yii\web\View */
/** @var $status integer */

$this->title = 'Платежи';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div class="row payment-index">
    <div class="col-xs-12">
        <?= Nav::widget([
            'options' => ['class' => 'nav nav-tabs material-tabs'],
            'items' => [
                [
                    'label' => PaymentHelper::getStatusText(Payment::STATUS_VALID),
                    'url' => ['payment/index', 'status' => Payment::STATUS_VALID],
                    'active' => (int)$status === Payment::STATUS_VALID,
                ],
                [
                    'label' => PaymentHelper::getStatusText(Payment::STATUS_INVALID),
                    'url' => ['payment/index', 'status' => Payment::STATUS_INVALID],
                    'active' => (int)$status === Payment::STATUS_INVALID,
                ],
            ],
        ]) ?>
        <div style="padding: 20px 0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'columns' => [
                    'rate.title',
                    'user.profileName',
                    'payment',
                    'finish',
                ],
            ]) ?>
        </div>
    </div>

</div>