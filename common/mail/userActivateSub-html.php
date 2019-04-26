<?php

/** @var $user common\models\User */
/** @var $rate common\models\Rate */

use yii\helpers\Html;

?>
<div class="activate-subscription">
    <p>Привет <?= Html::encode($user->username) ?>,</p>
    <p>Успешно активирована подписка - <?= $rate->title ?></p>
</div>
