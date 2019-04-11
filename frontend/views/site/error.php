<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$title = $name;
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
?>
<div class="container">
    <div class="site-error text-center">
        <h2><?= Html::encode($title) ?></h2>
        <p><?= nl2br(Html::encode($message)) ?></p>
        <?= Html::a('На главную', ['/site/index'], ['class' => 'btn']) ?>
    </div>
</div>
