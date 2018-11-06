<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<!doctype html>
<html lang="en">
<head>
	<title>Cinagro</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Varela+Round" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans|Noto+Sans|PT+Sans+Narrow|Source+Sans+Pro" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans|M+PLUS+Rounded+1c:700|Noto+Sans|PT+Sans+Narrow|Source+Sans+Pro" rel="stylesheet">
	<!-- Framework Css --> 
	<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web') ?>/template/css/lib/bootstrap.min.css">

	<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web') ?>/template/css/lib/bootstrap-grid.css">

	<!-- Font Awesome / Icon Fonts -->
	<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web') ?>/template/fonts/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web') ?>/template/css/style.css">
	<!-- TODO -->
	<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web') ?>/template/css/responsive.css">
	<title>Истории</title>
</head>
<body>
	<div class="wrapper">
		<!--===================== Header ========================-->
		<header>
			<div class="top-bar bg-black">
				<div class="container-large">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 text-right">
							<ul class="cst-margin-none">
								<li><a href="#">Вход</a></li>
								<li><a href="#">Регистрация</a></li>
							</ul><!--right-top-bar-->
						</div>
					</div>
				</div>
			</div><!--top-bar-->
			<div class="container header">
				<div class="row">
					<div class="col-md-2 col-sm-3 col-xs-3">
						<div class="logo"><a href="index.html"><img src="<?= Yii::getAlias('@web') ?>/template/img/logo.png" alt="logo"></a></div>
					</div>
					<div class="col-md-8 col-sm-8 col-xs-8 text-left">
						<ul class="menu cst-margin-none">
							<li><a href="index.html">Мои истории</a></li>
							<li><a href="index.html">Все</a></li>
							<li class="children">
								<a href="about.html">Подброки</a>
								<ul class="sub-menu">
									<li><a href="#">Научно-познавательные</a></li>
									<li><a href="#">Веселые</a></li>
									<li><a href="#">Для самых маленьких</a></li>
								</ul><!--sub-menu-->
							</li>
							<li><a href="index.html">Популярные</a></li>	
							<li><a href="index.html">Веселое</a></li>
							<li><a href="index.html">Еще пункт меню</a></li>						
						</ul><!--menu-->
						<button type="button" class="menu-button">
							<span></span>
						</button>
					</div>
				</div>
			</div>
		</header>
		<!--============== End of Header ========================-->
		<!--================= Breadcrumb ====================-->
		<div class="breadcrumb-top bg-yellow">
			<div class="container">
				<h2>Моя подписка</h2>
				<ol class="breadcrumb">
					<li><a href="#">Главная</a></li>
					<li class="active">Подписка</li>
				</ol><!--breadcrumb-->
			</div>
		</div><!--breadcrumb-top-->
		<!--====================== Posts ==========================-->
		<div class="container">
			<div class="customers">
				<div class="title-head">
					<p>Улучши возможность просмотра историй</p>
				</div>
				<div class="row">
                    <?php foreach($rates as $rate) {  ?>
                    <div class="col-md-4">
						<div class="inside big-banner cst-sub">
							<p class="cst-padding-none"><?= $rate->title ?></p>
							<p class="cst-cost"><?= $rate->cost ?> ₽</p>
							<?php $form = ActiveForm::begin(['id' => 'rate-form', 'action' => ['rate/payment'],]); ?>
								<div class="form-group text">
									<?= $form->field($rate, 'id') ?>
									<?= Html::submitButton('Купить', ['class' => 'custom-btn text-center white', 'name' => 'rate-button']) ?>
								</div>
							<?php ActiveForm::end(); ?>

							<div class="inside-inside">
								<span><?= $rate->description ?></span>
							</div>
						</div><!--inside-->
					</div>
                    <?php } ?>
				</div>
			</div><!--customers-->
		</div>
		<!--====================== Posts ==========================-->
		<!--===================== Footer ========================-->
		<footer class="bg-yellow">
			<div class="container">
				<div class="copyright">
					<p>Copyright &copy; 2018.</p>
				</div>
			</div>
			<div id="back-to-top"><i class="fa fa-angle-up"></i></div>
		</footer>
		<!--================= End of Footer =====================-->
	</div>
	<script type="text/javascript" src="<?= Yii::getAlias('@web') ?>/template/js/lib/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="<?= Yii::getAlias('@web') ?>/template/js/lib/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= Yii::getAlias('@web') ?>/template/js/lib/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="<?= Yii::getAlias('@web') ?>/template/js/main.js"></script>
</body>
</html>
