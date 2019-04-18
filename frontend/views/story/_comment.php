<?php

use yii\helpers\Html;
use common\helpers\SmartDate;

/* @var $model common\models\Comment */

?>
<div class="comment-list-item">
    <div class="comment-logo">
        <img src="/img/avatar.png" alt="">
    </div>
    <div class="comment">
        <div class="comment-header">
            <strong><?= $model->user->username ?></strong>
            <span><?= SmartDate::dateSmart($model->created_at, true) ?></span>
        </div>
        <div class="comment-body"><?= Html::encode($model->body) ?></div>
    </div>
</div>