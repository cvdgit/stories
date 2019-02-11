<?php

use yii\helpers\Html;
use frontend\widgets\StoryWidget;

/* @var $this yii\web\View */

$this->title = 'Сервис ускоренного развития речи ребёнка | wikids.ru';
?>

<div class="container-fluid">
    <div class="big-banner" style="background: url(/images/wikids-main.jpg) no-repeat center;margin-bottom: 40px">
        <div class="row">
            <div class="col-md-6 col-sm-12" style="display: block">
                <div class="text">
                    <h2>Сервис ускоренного развития<br>речи ребёнка - wikids.ru</h2>
                    <h3 class="description-subtitle">Посмотри короткий ролик о сервисе или зарегистрируйся</h3>
                    <?php if (Yii::$app->user->isGuest): ?>
                    <?= Html::a('Регистрация', ['/site/signup'], ['class' => 'custom-btn text-center white']) ?>
                    <?php else: ?>
                    <?= Html::a('Каталог историй', ['/story/index'], ['class' => 'custom-btn text-center white']) ?>
                    <?php endif ?>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 demo">
                <div class="demo-content">
                    <div class="browser">
                        <div class="browser-header">
                            <div class="browser-header-dot"></div>
                            <div class="browser-header-dot"></div>
                            <div class="browser-header-dot"></div>
                        </div>
                        <div class="browser-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-large">
    <article class="post gallery-post" style="margin-bottom: 0px">
        <div class="entry-content">
            <div class="title-head">
                <h2 class="text-black">О портале</h2>
            </div>
            <p>Чем лучше человек разговаривает, тем более он успешен в жизни.</p>
            <p>Надо объяснить любимой девушке, что именно ты – самый лучший.<br>
Надо донести до начальников, что именно тебе надо доверить самую сложную и дорогую работу.<br>
Надо объяснить своим коллегам по работе, что они должны сделать, ради общего дела.<br>
Надо найти добрые слова для своих друзей и быть желанным в любой компании.</p>
            <p>Если нашёл слова – то у тебя девушка есть, высокая зарплата, интересная работа и друзья.</p>
            <p>Подарите всё это своим детям. Научите их говорить.<br>
Чем раньше начнёте – тем лучше.<br>
Это может сделать только родитель.<br>
Телевизору такое не под силу.</p>
            <p>Наш портал – для достижения этого успеха.</p>
            <p>Лучше сейчас уделить своему маленькому умнице час перед сном, чем потом тратить деньги на репетиторов.<br>
Да и не помогут репетиторы. Сами знаете.
</p>
            <p>Заниматься с детьми можно в формате – сказка на ночь. Не с целью усыпить детей, а с целью научить их русской речи.<br>
Скоро Вы заметите, как они начнут разговаривать теми словами, которые Вы донесли до них через сказки. И тогда Вы почувствуете, что реально открываете им дорогу в будущее.<br>
Успехов Вам и Вашим детишкам!</p>
        </div>
    </article>
</div>

<div class="container-large">
    <?= StoryWidget::widget() ?>
</div>
