<?php

use backend\assets\AppAsset;
use common\models\News;
use common\models\test\SourceType;
use common\rbac\UserRoles;
use common\widgets\ToastrFlash;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View $this
 * @var string $content
 */

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
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Перейти к сайту',
        'brandUrl' => '/',
        'innerContainerOptions' => ['class' => 'container-fluid'],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    NavBar::end();
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <?php if (isset($this->params['sidebarMenuItems']) && count($this->params['sidebarMenuItems']) > 0): ?>
                <?= Nav::widget([
                    'options' => ['class' => 'nav-sidebar'],
                    'items' => $this->params['sidebarMenuItems'],
                ]) ?>
                <?php endif ?>
                <?php if (Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL)): ?>
                <?= Nav::widget([
                    'options' => ['class' => 'nav-sidebar'],
                    'items' => [
                        ['label' => 'Главная', 'url' => ['/site/index']],
                        ['label' => 'Заявки с формы', 'url' => ['/contact-request/index']],
                        ['label' => 'Истории', 'url' => ['/story/index']],
                        ['label' => 'Списки историй', 'url' => ['/story-list/index'], 'active' => Yii::$app->controller->id === 'story-list'],
                        ['label' => 'Разделы', 'url' => ['/section']],
                        ['label' => 'Категории', 'url' => ['/category/index'], 'active' => Yii::$app->controller->id === 'category'],
                        ['label' => 'Пользователи', 'url' => ['/user/index']],
                        ['label' => 'Обратная связь', 'url' => ['/feedback/index']],
                        ['label' => 'Комментарии', 'url' => ['/comment/index']],
                        ['label' => 'Подписки', 'url' => ['/rate/index']],
                        ['label' => 'Блог', 'url' => ['/news/admin', 'status' => News::STATUS_PROPOSED], 'active' => Yii::$app->controller->id === 'news'],
                        ['label' => 'Тесты', 'url' => ['/test/index', 'source' => SourceType::TEST], 'active' => Yii::$app->controller->id === 'test'],
                        ['label' => 'Видео', 'url' => ['/video/index'], 'active' => Yii::$app->controller->id === 'video'],
                        ['label' => 'Плейлисты', 'url' => ['/playlist/index'], 'active' => Yii::$app->controller->id === 'playlist'],
                        ['label' => 'Изображения', 'url' => ['/image/index'], 'active' => Yii::$app->controller->id === 'image'],
                        ['label' => 'Списки слов', 'url' => ['/word-list/index'], 'active' => Yii::$app->controller->id === 'word-list'],
                        ['label' => 'Группы', 'url' => ['/study-group/index'], 'active' => Yii::$app->controller->id === 'study-group'],
                        ['label' => 'Задания', 'url' => ['/study-task/index'], 'active' => Yii::$app->controller->id === 'study-task'],
                        ['label' => 'Аудио файлы', 'url' => ['/audio-file/index']],
                        ['label' => 'Папки', 'url' => ['/files/study-folder/index']],
                        ['label' => 'Файлы', 'url' => ['/files/study-file/index']],
                        ['label' => 'Обучение', 'url' => ['/edu/admin/default/index']],
                    ],
                ]) ?>
                <?php endif ?>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?= Breadcrumbs::widget([
                        'homeLink' => false,
                    'links' => $this->params['breadcrumbs'] ?? [],
                ]) ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<?= ToastrFlash::widget() ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
