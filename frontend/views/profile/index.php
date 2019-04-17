<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;
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
            <div class="col-md-3">
                <div class="text-center">
                    <?php $background = (isset($model->image)) ? '/uploads/' . $model->image : 'http://ssl.gstatic.com/accounts/ui/avatar_2x.png';  ?>
                    <div class="cst-box-image">
                        <div class="cst-image-div" id="image-preview" style="background-image: url(<?= $background  ?>)"></div>
                    </div>
                    <!--p>Загрузить другую фотографию...</p>
                    <div class="file-upload">
                        <form id="form-upload">
                            <label>
                                <input type="file" id="image-upload" name="image-upload" accept="image/*,image/jpeg">
                                <span id="fileButton">Выбрать файл</span>
                            </label>
                        </form>
                    </div>
                    <input type="text" id="filename" class="filename" disabled-->
                </div>
            </div>
            <div class="col-md-9">
                <?= Tabs::widget([
                    'class' => 'profile-tabs',
                    'items' => [
                        ['label' => 'Подписка',
                         'content' => $this->render('_tab_payments', ['activePayment' => $activePayment]),
                         'active' => true],
                        ['label' => 'Основная инфомация',
                            'content' => $this->render('_tab_general', ['model' => $model])],
                    ],
                ]) ?>
            </div>
        </div>
    </main>
</div>