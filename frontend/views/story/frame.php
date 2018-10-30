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

//AppAsset::register($this);
//$this->registerCssFile('/css/site.css');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="sl-root decks export offline loaded">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?php $this->head() ?>
</head>
<body class="reveal-viewport theme-font-montserrat">
<?php $this->beginBody() ?>
<?= RevealWidget::widget(['data' => $model->body]) ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
