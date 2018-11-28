<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = 'История - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Каталог историй', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="vertical-slider">
	<div class="container">
		<div class="row">
			<div class="col-md-7" style="height: 100%; min-height: 100%">
				<iframe border="0" width="100%" height="500" style="border: 0 none" src="/story/viewbyframe/<?= $model->id ?>"></iframe>
			</div>
			<div class="col-md-5">
				<div class="inside-single cst-padding-0">
					<h4 class="title"><?= Html::encode($model->title) ?></h4>
					<!-- <div class="star">
						<span>
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
							<i class="fa fa-star"></i>
						</span>
						<a href="#">(4 customer reviews)</a>
					</div> -->
					<!-- <div class="price">$9.99</div> -->
					<div class="description">
						<p><?= Html::encode($model->body) ?></p>
					</div>
					<ul>
						<li>Категория: <?= Html::a($model->category->name, ['story/category', 'category' => $model->category->alias]) ?></li>
						<li>Тэги:
						<?php foreach($model->getTags()->all() as $tag): ?>
							<?= Html::a($tag->name, ['tag', 'tag' => $tag->name]) ?>
						<?php endforeach ?>
						</li>
						<!-- <li>Share:
							<ul class="social-icon">
								<li class="facebook"><a href="#"><i class="fab fa-facebook"></i></a></li>
								<li class="google"><a href="#"><i class="fab fa-google-plus"></i></a></li>
								<li class="tumblr"><a href="#"><i class="fab fa-tumblr"></i></a></li>
								<li class="instagram"><a href="#"><i class="fab fa-instagram"></i></a></li>
							</ul>
						</li> -->
						<li>Тип: <a href="#">Бесплатно</a></li>
					</ul>
				</div>
			</div>
			<!-- <div class="col-md-12">
				<ul class="nav nav-tabs text-center">
					<li class="active"><a href="#description" data-toggle="tab" aria-expanded="true">Description</a></li>
					<li><a href="#additional-info" data-toggle="tab">Additional Info</a></li>
					<li><a href="#shipping" data-toggle="tab">Shipping</a></li>
					<li><a href="#reviews" data-toggle="tab">Reviews (4)</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="description">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo. </p>
						<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.   Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione.</p>
						<ul class="list">
							<li class="first">Benefits of Essential Oils:</li>
							<li><i class="fa fa-circle"></i>Improve immunity and speed illness recovery</li>
							<li><i class="fa fa-circle"></i>Deal with infection (under the care of a professional)</li>
							<li><i class="fa fa-circle"></i>Make homemade cleaning or beauty products</li>
							<li><i class="fa fa-circle"></i>In recipes like homemade bug spray to avoid outdoor pests naturally</li>
						</ul>
					</div>
					<div class="tab-pane fade" id="additional-info">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo. </p>
					</div>
					<div class="tab-pane fade" id="shipping">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo. </p>
					</div>
					<div class="tab-pane fade" id="reviews">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo. </p>
					</div>
				</div>
				<h2 class="title-head text-center">similar products</h2>
				<div class="similar-products owl-carousel owl-theme">
					<div class="item">
						<div class="product">
							<div class="images">
								<a href="single-product.html"><img src="http://via.placeholder.com/160x210" alt="product2"></a>
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
				</div>
			</div> -->
		</div>
	</div>
</div>