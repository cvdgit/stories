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
}
.big-banner .text h3 {
    color: #fff;
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
    <div class="big-banner">
        <div class="row">
            <div class="col-md-6 col-sm-12" style="display: block">
                <div class="text">
                    <h2>Сервис ускоренного развития<br>речи ребёнка - wikids.ru</h2>
                    <h3 class="description-subtitle">Посмотри короткий ролик о сервисе или зарегистрируйся</h3>
                    <?= Html::a('Регистрация', ['/site/signup'], ['class' => 'custom-btn text-center white']) ?>
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

<div class="container">
    <div class="category-product">
        <ul>
            <li>
                <a href="shop.html">
                    <img src="http://via.placeholder.com/115x150" alt="groceries">
                    <span>Natural</span>
                </a>
            </li>
            <li class="center">
                <a href="shop.html">
                    <img src="http://via.placeholder.com/115x150" alt="pineapple">
                    <span>organic</span>
                </a>
            </li>
            <li>
                <a href="shop.html">
                    <img src="http://via.placeholder.com/115x150" alt="corn">
                    <span>health</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="container-large">
    <div class="product-slider">
        <div class="title-head">
            <h2 class="text-black">What's Trending</h2>
            <p>Be Healty Organic Food</p>
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
            <a href="shop.html" class="custom-btn text-center green"><span>View the store</span></a>
        </div>
    </div>
</div>

<div class="container-large">
    <div class="big-banner">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="text">
                    <h2>Unlock your potential<br>with good nutrion</h2>
                    <p>Be Healty Organic Food</p>
                    <a href="#" class="custom-btn text-center white">view recipes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-large">
    <div class="grid-product">
        <div class="title-head">
            <h2 class="text-black">Our Product</h2>
            <p>Be Healty Organic Food</p>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">ORS - Olive Oil</a>
                        <span class="price">
                            <del>$9.99</del>
                            <ins>$6.99</ins>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Organic - Agave Five</a>
                        <span class="price">$9.99</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Organic Girl - Romanie</a>
                        <span class="price">$4.79</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Beach Nut - Coldpure</a>
                        <span class="price">$6.49</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Beech Nut - Just Pumpkin</a>
                        <span class="price">$9.69</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Detox Zero</a>
                        <span class="price">$16.99</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Mooala Original</a>
                        <span class="price">$10.99</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="product">
                    <div class="images">
                        <a href="single-product.html"><img src="http://via.placeholder.com/160x230" alt="product5"></a>
                        <div class="button-group">
                            <a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
                            <a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                    <div class="info-product">
                        <a href="single-product.html" class="title">Low Cow - Lite Ice Cream</a>
                        <span class="price">$9.99</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center"><a href="shop.html" class="custom-btn text-center green">VIEW THE STORE</a></div>
    </div>

    <div class="banner-img">
        <div class="row">
            <div class="col-md-6">
                <div class="banner-inside">
                    <img src="http://via.placeholder.com/800x330" alt="banner">
                    <div class="inside text-right">
                        <h2 class="text-black">gift Certificate</h2>
                        <p>Give the perfect gift every time</p>
                        <a href="blog.html" class="custom-btn text-center white">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="banner-inside">
                    <img src="http://via.placeholder.com/800x330" alt="banner">
                    <div class="inside text-right">
                        <h2 class="text-black">Fresh Fruits</h2>
                        <p>100% freshness guarantee </p>
                        <a href="blog.html" class="custom-btn text-center white">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner-color">
        <div class="row">
            <div class="col-md-4">
                <div class="inside text-center bg-yellow">
                    <h5>Delivered to Your Door</h5>
                    <p>Skip the store: We ship it all right to you</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="inside text-center bg-yellow">
                    <h5>Top Organic &amp; Non-GMO</h5>
                    <p>Browse 4,000 products you know and love.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="inside text-center bg-yellow">
                    <h5>Save Time &amp; Money</h5>
                    <p>Save 25-50% on every item we carry.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-large">
    <div class="schedule">
        <div class="row">
            <div class="col-md-6 text-center">
                <div class="inside">
                    <div class="title-head">
                        <h2 class="text-black">Healthy Lunch</h2>
                        <p>Be Healty Organic Food</p>
                    </div>
                    <ul class="week nav nav-tabs">
                        <li><a href="#sunday" data-toggle="tab">S</a></li>
                        <li class="active"><a href="#monday" data-toggle="tab" aria-expanded="true">M</a></li>
                        <li><a href="#tuesday" data-toggle="tab">T</a></li>
                        <li><a href="#wednesday" data-toggle="tab">W</a></li>
                        <li><a href="#thursday" data-toggle="tab">T</a></li>
                        <li><a href="#friday" data-toggle="tab">F</a></li>
                        <li><a href="#saturday" data-toggle="tab">S</a></li>
                    </ul><!--week-->
                    <div class="tab-content">
                        <div class="tab-pane fade" id="sunday">
                            <ul>
                                <li class="first">sunday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                        <div class="tab-pane fade in active" id="monday">
                            <ul>
                                <li class="first">monday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="tuesday">
                            <ul>
                                <li class="first">tuesday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="wednesday">
                            <ul>
                                <li class="first">wednesday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="thursday">
                            <ul>
                                <li class="first">thursday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="friday">
                            <ul>
                                <li class="first">friday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="saturday">
                            <ul>
                                <li class="first">saturday:</li>
                                <li>4 oz. grilled chicken breast</li>
                                <li>1/2 cup sliced strawberries</li>
                                <li>1/2 cup steamed spinach w/ salt and pepper</li>
                                <li>1/2 cup brown rice, steamed</li>
                            </ul>
                        </div>
                    </div>
                    <a href="blog.html" class="custom-btn text-center green">order now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-large">
    <div class="posts">
        <div class="title-head">
            <h2 class="text-black">The Journal</h2>
            <p>Be Healty Organic Food</p>
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="blog.html"><img src="http://via.placeholder.com/387x440" alt="mini-post3"></a>
                <div class="content">
                    <span>in <a href="blog.html">NUTRITION + WELLNESS</a>/<a href="#">September 05, 2017</a></span>
                    <a href="blog.html" class="title">Conventional Vs. Organic</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="blog.html"><img src="http://via.placeholder.com/387x440" alt="mini-post3"></a>
                <div class="content">
                    <span>in <a href="blog.html">NUTRITION + WELLNESS</a>/<a href="#">September 05, 2017</a></span>
                    <a href="blog.html" class="title">How to use organic herbs for better sleep at night</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="blog.html"><img src="http://via.placeholder.com/387x440" alt="mini-post3"></a>
                <div class="content">
                    <span>in <a href="blog.html">NUTRITION + WELLNESS</a>/<a href="#">September 05, 2017</a></span>
                    <a href="blog.html" class="title">Review of door to door organic delivery service</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="blog.html"><img src="http://via.placeholder.com/387x440" alt="mini-post3"></a>
                <div class="content">
                    <span>in <a href="blog.html">NUTRITION + WELLNESS</a>/<a href="#">September 05, 2017</a></span>
                    <a href="blog.html" class="title">Just to brighten your day</a>
                </div>
            </div>
        </div>
        <div class="text-center">
            <a href="blog.html" class="custom-btn text-center green">view the journal</a>
        </div>
    </div>
    <div class="customers">
        <div class="title-head">
            <h2 class="text-black">Testimonials</h2>
            <p>Be Healty Organic Food</p>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="inside">
                    <img src="/images/stars.png" class="star" alt="stars">
                    <p>"I love your system. Wow what great  service, I love it! I will recommend you to<br>my colleagues.<br>I have gotten at least 50 times the value from food."</p>
                    <a href="#">
                        <div class="user">
                            <img src="http://via.placeholder.com/60x60" alt="user1">
                            <div class="inside-inside">
                                <span class="name">Pauline Norman</span>
                                <span>Melbourne, FL</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="inside">
                    <img src="/images/stars.png" class="star" alt="stars">
                    <p>"Food is worth much more than I paid.  I like food more and more each day  because it makes my life a lot easier.  Thank You! I have gotten at least 50 times the value from food."</p>
                    <a href="#">
                        <div class="user">
                            <img src="http://via.placeholder.com/60x60" alt="user1">
                            <div class="inside-inside">
                                <span class="name">Juana Duncan</span>
                                <span>Orlando, FL</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="inside">
                    <img src="/images/stars.png" class="star" alt="stars">
                    <p>"Best. Product. Ever! I couldn't have asked for more than this. Food is the most valuable business resource we have <br>EVER purchased.<br>I can't say enough about food."</p>
                    <a href="#">
                        <div class="user">
                            <img src="http://via.placeholder.com/60x60" alt="user1">
                            <div class="inside-inside">
                                <span class="name">Gail Butler</span>
                                <span>Orlando, FL</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>