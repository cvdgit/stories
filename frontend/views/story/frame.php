<?php

/* @var $this \yii\web\View */
/* @var $model common\models\Story */

use yii\helpers\Html;
use common\widgets\RevealWidget;
use frontend\assets\StoryFrameAsset;

if (class_exists('yii\debug\Module')) {
    $this->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
}

StoryFrameAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="sl-root decks export offline loaded">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

</head>
<body class="reveal-viewport theme-font-montserrat" style="padding-bottom: 50px">
<?php if ($model): ?>
    <?php $this->beginBody() ?>
    <?= RevealWidget::widget(['story_id' => $model->id, 'data' => $model->body]) ?>
    <?php $this->endBody() ?>
    <div class="story-controls"></div>
<?php endif ?>
</body>
</html>
<?php $this->endPage() ?>