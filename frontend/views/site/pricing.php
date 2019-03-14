<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$title = 'Подписки';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
$this->params['breadcrumbs'][] = $title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Подписки</p>
</div>
