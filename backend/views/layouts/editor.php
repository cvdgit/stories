<?php
/* @var $this yii\web\View */
/* @var $content string */
use backend\assets\AppAsset;
use common\rbac\UserRoles;
use common\widgets\ToastrFlash;
use yii\helpers\Html;
use yii\helpers\Json;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
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
                width: 100%;
                height: 100%;
            }
            body {
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
                overflow: hidden;
                font-size: 18px;
            }
            ::-webkit-scrollbar {
                width: 12px;
                height: 12px
            }

            ::-webkit-scrollbar-thumb:vertical {
                min-height: 16px;
                border: 2px solid transparent;
                border-radius: 8px;
                background-color: rgba(100,100,100,0.6);
                background-clip: padding-box
            }

            ::-webkit-scrollbar-thumb:horizontal {
                min-width: 16px;
                border: 2px solid transparent;
                border-radius: 8px;
                background-color: rgba(100,100,100,0.6);
                background-clip: padding-box
            }

            ::-webkit-scrollbar-thumb:vertical:hover,
            ::-webkit-scrollbar-thumb:vertical:active,
            ::-webkit-scrollbar-thumb:horizontal:hover,
            ::-webkit-scrollbar-thumb:horizontal:active {
                background-color: rgba(100,100,100,0.9)
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