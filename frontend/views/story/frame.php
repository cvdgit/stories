<?php

/* @var $this \yii\web\View */
/* @var $model common\models\Story */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\helpers\Url;
use common\widgets\RevealWidget;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="sl-root decks export offline loaded">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <!-- TODO: -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <?php $this->head() ?>
</head>
<body class="reveal-viewport theme-font-montserrat">
<?php if ($model): ?>
    <?php $this->beginBody() ?>
        <?= RevealWidget::widget(['data' => $model->body]) ?>
    <?php $this->endBody() ?>
<?php endif ?>
</body>
</html>
<?php $this->endPage() ?>