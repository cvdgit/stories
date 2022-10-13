<?php

declare(strict_types=1);

use common\widgets\ToastrFlash;
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use common\rbac\UserRoles;

AppAsset::register($this);

/** @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" id="viewport-meta" content="width=1024, user-scalable=0, viewport-fit=cover" />
    <?= Html::csrfMetaTags() ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= Yii::$app->params['metrica'] ?>
    <script>
        var WikidsConfig = {
            'user': {
                'isGuest': <?= Json::encode(Yii::$app->user->isGuest) ?>,
                'isModerator': <?= Json::encode(Yii::$app->user->can(UserRoles::ROLE_MODERATOR)) ?>
            }
        };
    </script>
    <style>
        html {
            min-height: 100%;
            position: relative;
        }
        body {
            height: 100%;
            margin: 0;
        }
        .story-box {
            z-index: 0;
            position: relative;
            margin: 15px auto 20px;
            width: 1060px;
            height: 600px;
            background: #FFFFFF;
            -moz-box-shadow: 0px 2px 19px 0px rgba(0,0,0,0.5),0px 2px 4px 0px rgba(0,0,0,0.5);
            -webkit-box-shadow: 0px 2px 19px 0px rgb(0 0 0 / 50%), 0px 2px 4px 0px rgb(0 0 0 / 50%);
            box-shadow: 0px 2px 19px 0px rgb(0 0 0 / 50%), 0px 2px 4px 0px rgb(0 0 0 / 50%);
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?= ToastrFlash::widget() ?>
<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>

