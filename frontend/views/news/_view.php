<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Markdown;
use yii\helpers\HtmlPurifier;

/** @var yii\web\View $this */
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

$isFull = isset($isFull) ? $isFull : false;
$displayStatus = isset($displayStatus) ? $displayStatus : false;
$displayUser = isset($displayUser) ? $displayUser : true;
$displayModeratorButtons = isset($displayModeratorButtons) ? $displayModeratorButtons : false;

/** @var $model common\models\News */
?>
<div class="row post">
    <div class="col-lg-2 col-md-3 col-sm-3 info">
        <p class="time"><?= Yii::$app->formatter->asDate($model->created_at) ?></p>
        <?php if ($displayUser && $model->user_id): ?>
            <p class="author">
                <?= \frontend\widgets\Avatar::widget(['user' => $model->user, 'size' => 48]) ?>
            </p>
            <p class="twitter-handle">
                <?= Html::encode($model->user->getProfileName()) ?>
            </p>
        <?php endif ?>
        <?php if ($displayModeratorButtons): ?>
            <?= Html::a(Yii::t('news', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php endif ?>
    </div>
    <div class="col-lg-7 col-md-9 col-sm-9 clearfix">
        <h2><?= Html::a(Html::encode($model->title), ['news/view', 'slug' => $model->slug]) ?></h2>
        <div class="content">
            <?= HtmlPurifier::process($model->text) ?>
        </div>
    </div>
</div>