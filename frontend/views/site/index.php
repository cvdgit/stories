<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'CVD - Сказки';
$css = <<< CSS
.big-banner {
    padding: 80px 0 50px;
}
.demo {
    position: relative;
    display: inline-block;
    margin: 0 auto;
    font-size: 10px;
    color: #f5f5f5;
}
.demo-content {
    opacity: 1;
    -webkit-transform: none;
    -ms-transform: none;
    transform: none;
}
.browser {
    width: 90%;
    height: 100%;
    box-shadow: 0 6px 30px rgba(0,0,0,0.1), 0 2px 6px rgba(0,0,0,0.15);
    margin: 0 auto;
}
.browser-header {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    position: relative;
    width: 100%;
    height: 35px;
    padding: 0 10px;
    background-color: currentColor;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: normal;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: start;
    -ms-flex-pack: start;
    justify-content: flex-start;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}
.browser-header-dot {
    width: 10px;
    height: 10px;
    border-radius: 10px;
    background-color: rgba(0,0,0,0.1);
    margin-right: 6px;
}
.browser-content {
    position: relative;
    height: 43.8em;
    background-color: #fff;
    border: 8px solid currentColor;
    border-top: 0;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
    box-sizing: content-box;
}

.big-banner .text h2 {
    margin-bottom: 0.2em;
    font-size: 2.4em;
    font-weight: 600;
    line-height: 1.1em;
    color: #000;
}
.big-banner .text h3 {
    color: #000;
    font-family: 'CirceBlack', serif;
}
.big-banner .text {
    display: block;
    position: relative;
    margin: 4em auto 2em auto;
    font-size: 19px;
    max-width: 800px;
    padding-left: 2em;
}
.description-subtitle {
    margin-top: 1em;
    margin-bottom: 2em;
    font-size: 1em;
}
CSS;

$this->registerCss($css);
?>

<div class="container-fluid">
    <div class="big-banner" style="background: url(/images/wikids-main.jpg) no-repeat center">
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
    <div class="product-slider">
        <div class="title-head">
            <h2 class="text-black">Доступные истории</h2>
        </div>
        <div class="slider-product owl-carousel owl-theme">
            <div class="item">
                <div class="product">
                    <div class="images text-center">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product4"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Over the Moo - Ice Cream</a>
                        <span class="price">$7.99</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <!--a href="shop.html" class="custom-btn text-center green"><span>View the store</span></a-->
            <?= Html::a(Html::tag('span', 'Посмотреть все истории'), ['/story/index'], ['class' => 'custom-btn text-center green']) ?>
        </div>
    </div>
</div>
