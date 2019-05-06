<?php

use yii\helpers\Html;
use common\helpers\SmartDate;

/* @var $model common\models\Comment */

?>
<div class="comment-list-item">
    <div class="comment-logo">
        <?php
        $image = '/img/avatar.png';
        $profile = $model->user->profile;
        if ($profile !== null) {
            $profilePhoto = $profile->profilePhoto;
            if ($profilePhoto !== null) {
                $image = $profilePhoto->getThumbFileUrl('file', 'list', '/img/avatar.png');
            }
        }
        ?>
        <?= Html::img($image) ?>
    </div>
    <div class="comment">
        <div class="comment-header">
            <strong><?= $model->user->username ?></strong>
            <span><?= SmartDate::dateSmart($model->created_at, true) ?></span>
        </div>
        <div class="comment-body"><?= Html::encode($model->body) ?></div>
    </div>
</div>