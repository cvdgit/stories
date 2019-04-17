<?php

/** @var $model common\models\User */

use yii\helpers\Html;

?>
<div class="profile-tab-content">
    <p><strong>Имя пользователя:</strong> <?= Html::encode($model->username) ?></p>
    <p><strong>Email:</strong> <?= Html::encode($model->email) ?></p>
</div>
