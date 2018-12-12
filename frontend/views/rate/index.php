<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Подписки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="customers">
        <div class="title-head">
            <p>Улучши возможность просмотра историй</p>
        <?php if (isset($count_date_rate)): ?>
            <p>Подписк : <?= $count_date_rate ?></p>
        <?php endif ?>
        </div>
        <div class="row">
            <?php foreach($rates as $rate) {  ?>
            <div class="col-md-4">
                <div class="inside cst-sub">
                    <p class="cst-padding-none"><?= $rate->title ?></p>
                    <p class="cst-cost"><?= $rate->cost ?> ₽</p>
                    
                        <form action='<?= $rate->dataPayment['url'] ?>' method=POST>
                            <input type=hidden name=MrchLogin value='<?= $rate->dataPayment['MrchLogin'] ?>'>
                            <input type=hidden name=OutSum value='<?= $rate->cost ?>'>
                            <input type=hidden name=InvId value='<?= $rate->dataPayment['InvId'] ?>'>
                            <input type=hidden name=Desc value='<?= $rate->dataPayment['Desc'] ?>'>
                            <input type=hidden name=SignatureValue value='<?= $rate->dataPayment['SignatureValue'] ?>'>
                            <input type=hidden name=Shp_item value='<?= $rate->id ?>'>
                            <input type=hidden name=IncCurrLabel value='<?= $rate->dataPayment['IncCurrLabel'] ?>'>
                            <input type=hidden name=Culture value='<?= $rate->dataPayment['Culture'] ?>'>
                            <?php if(isset($user)): ?>
                                <input type=submit value='<?= isset($count_date_rate) ? "Продлить" : "Купить"?>' class="cst-btn text-center white">
                            <?php else: ?>
                                <?= Html::a('Купить', ['/signup'], ['class' => 'cst-btn']) ?>
                            <?php endif ?>
                        </form>

                    <div class="inside-inside">
                        <span><?= $rate->description ?></span>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>