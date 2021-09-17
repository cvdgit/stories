<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $activePayment common\models\Payment */
$title = 'Профиль пользователя';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
?>
<h1><span>Основная</span> информация</h1>
<p><strong>Email:</strong> <?= Html::encode($model->email) ?></p>
<p class="text-center" style="margin: 40px 0">
    <?= Html::a('Редактировать профиль', ['/profile/update'], ['class' => 'btn btn-small']) ?>
</p>