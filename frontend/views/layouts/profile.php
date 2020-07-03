<?php $this->beginContent('@frontend/views/layouts/main.php') ?>
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3 col-sm-6 col-sm-offset-3 col-md-3 col-md-offset-0 col-lg-3 col-lg-offset-0">
            <!--div-- class="text-center">
                <h3 style="margin-top: 12px">username</h3>
                <div class="cst-box-image text-center">
                    <div class="cst-image-div" id="image-preview" style="background-image: url('/img/no_avatar.png')"></div>
                </div>
            </div-->
            <?= \yii\widgets\Menu::widget([
                'items' => [
                    ['label' => '<i class="glyphicon glyphicon-user"></i> Основная информация', 'url' => ['/profile'], 'active' => Yii::$app->controller->id === 'profile'],
                    ['label' => '<i class="glyphicon glyphicon-education"></i> Обучение', 'url' => ['/training'], 'active' => Yii::$app->controller->id === 'training'],
                    ['label' => '<i class="glyphicon glyphicon-ruble"></i> Подписки', 'url' => ['/payment/index'], 'active' => Yii::$app->controller->id === 'payment'],
                ],
                'options' => ['class' => 'profile-menu'],
                'encodeLabels' => false,
            ]) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <main class="site-user-profile">
                <?= $content ?>
            </main>
        </div>
    </div>
</div>
<?php $this->endContent() ?>