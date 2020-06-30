<?php

use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $activePayment common\models\Payment */

$title = 'Профиль пользователя';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
?>
<div class="container">
    <main class="site-user-profile">
        <h1><span>Профиль</span> пользователя</h1>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3 col-sm-6 col-sm-offset-3 col-md-3 col-md-offset-0 col-lg-3 col-lg-offset-0">
                <div class="text-center">
                    <h3 style="margin-top: 12px"><?= $model->getProfileName() ?></h3>
                    <?php
                    $background = '/img/no_avatar.png';
                    if ($model->profile !== null) {
                        $profilePhoto = $model->profile->profilePhoto;
                        if ($profilePhoto !== null) {
                            $background = $profilePhoto->getThumbFileUrl('file', 'profile', '/img/no_avatar.png');
                            $background .= '?v=' . $profilePhoto->version;
                        }
                    }
                    ?>
                    <div class="cst-box-image text-center">
                        <div class="cst-image-div" id="image-preview" style="background-image: url(<?= $background  ?>)"></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?= Tabs::widget([
                    'options' => ['class' => 'profile-tabs'],
                    'items' => [
                        ['label' => 'Основная информация',
                         'content' => $this->render('_tab_general', ['model' => $model, 'activePayment' => $activePayment]),
                         'active' => true],
                        ['label' => 'История подписок',
                            'content' => $this->render('_tab_payments', ['payments' => $model->payments])],
                        ['label' => 'Ученики',
                            'content' => $this->render('_tab_students', ['students' => $model->getStudentsAsArray()])],
                    ],
                ]) ?>
            </div>
        </div>
    </main>
</div>