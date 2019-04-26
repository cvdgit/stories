<?php

/** @var $user common\models\User */
/** @var $rate common\models\Rate */

?>
    Привет <?= $user->username ?>,

    Успешно активирована подписка - <?= $rate->title ?>