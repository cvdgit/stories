<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div class="error-page">
    <div class="container-large">
        <div class="inside text-center">
            <h2><?= Html::encode($this->title) ?></h2>
            <p><?= nl2br(Html::encode($message)) ?></p>
            <?= Html::a('back to home page', ['/site/index'], ['class' => 'custom-btn green']) ?>
        </div>
    </div>
</div>
