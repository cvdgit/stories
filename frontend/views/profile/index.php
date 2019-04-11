<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$title = 'Профиль пользователя';
$this->setMetaTags($title,
                   $title,
                   '',
                   $title);
?>
<div class="container">
    <main class="site-user-profile">
        <h4><?= $model->username ?></h4>
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-9">
                <?= Tabs::widget([
                    'items' => [
                        ['label' => 'Основная инфомация',
                         'content' => $this->render('_tab_general', ['model' => $model]),
                         'active' => true],
                        ['label' => 'Подписка',
                         'content' => $this->render('_tab_payments', ['model' => $model])],
                    ],
                ]) ?>
            </div>
        </div>
    </main>
</div>