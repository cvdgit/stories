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
					<!--div class="col-md-5 col-sm-4 col-xs-4">
						< ! -- TODO -- >
						<div style="text-align: center;"><p style="text-transform: uppercase; color: #1e1e1e; font-size: 20px; position: relative; margin-right: 100px;">Сказочки</p><p style="text-transform: uppercase; color: #1e1e1e; font-size: 20px; position: relative;">рассказочки</p></div>
					</div-->
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
	</div>
	
    <div class="page home-page">
        <!--*********************************************************************************************************-->
        <!--************ HERO ***************************************************************************************-->
        <!--*********************************************************************************************************-->
        <header class="hero">
            <div class="hero-wrapper">
                <!--============ Hero Form ==========================================================================-->
                <form class="hero-form form">
                    <div class="container">
                        <!--Main Form-->
                        <div class="main-search-form">
                            <div class="form-row">
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-group">
										<ul class="simplefilter">
											<li data-filter="all">Все</li>
											<li class="active" data-filter="1">Бесплатные</li>
											<li data-filter="2">Подписки</li>
										</ul>
                                    </div>
                                    <!--end form-group-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group">
										<input name="keyword" type="text" class="form-control" id="what" placeholder="Поиск...">
										<span class="geo-location input-group-addon" data-toggle="tooltip" data-placement="top" title="Find My Position"><i class="fa fa-search"></i></span>
                                    </div>
                                    <!--end form-group-->
                                </div>
                                <!--end col-md-3-->
                                <div class="col-md-3 col-sm-3">
                                    <button type="submit" class="btn btn-primary width-100">Поиск</button>
                                </div>
                                <!--end col-md-3-->
                            </div>
                            <!--end form-row-->
                        </div>
                        <!--end main-search-form-->
                        <!--Alternative Form-->
                        <div class="alternative-search-form">
                            <a href="#collapseAlternativeSearchForm" class="icon" data-toggle="collapse"  aria-expanded="false" aria-controls="collapseAlternativeSearchForm"><i class="fa fa-plus"></i>Показать все</a>
                            <div class="collapse" id="collapseAlternativeSearchForm">
                                <div class="wrapper">
                                    <div class="form-row">
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 d-xs-grid d-flex align-items-center justify-content-between">
											<div class="form-group">
												<a href="#" class="custom-btn green">Новинки</a>
												<a href="#" class="custom-btn green active">Для самых маленьких</a>												
												<a href="#" class="custom-btn green">Развивающие</a>
												<!-- TODO: на новую строку-->
												<!-- <a href="#" class="custom-btn green">Сказки для всей семьи</a> -->
											</div>
                                        </div>
                                        <!--end col-xl-6-->
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-row">
                                                <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
														<select name="distance" id="distance" class="small" data-placeholder="Все жанры" >
															<option value="">Все жанры</option>
															<option value="1">приключение</option>
															<option value="2">музыкальные</option>
															<option value="3">развавающие</option>
														</select>
                                                    </div>
                                                    <!--end form-group-->
                                                </div>
                                                <!--end col-md-4-->
                                                <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
														<select name="distance" id="distance" class="small" data-placeholder="Все годы" >
															<option value="">Все годы</option>
															<option value="1">2018 год</option>
															<option value="2">2017 год</option>
															<option value="3">2016 год</option>
															<option value="4">2014-2015</option>
															<option value="5">2000-2014</option>
														</select>
                                                    </div>
                                                    <!--end form-group-->
                                                </div>
                                                <!--end col-md-4-->
                                                <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <select name="distance" id="distance" class="small" data-placeholder="Все авторы" >
                                                            <option value="">Все авторы</option>
                                                            <option value="1">Иванов А.В.</option>
                                                            <option value="2">Сидоров К.Е.</option>
                                                            <option value="3">Антонов Ж.Д.</option>
                                                        </select>
                                                    </div>
                                                    <!--end form-group-->
                                                </div>
                                                <!--end col-md-3-->
                                            </div>
                                            <!--end form-row-->
                                        </div>
                                        <!--end col-xl-6-->
                                    </div>
                                    <!--end row-->
                                </div>
                                <!--end wrapper-->
                            </div>
                            <!--end collapse-->
                        </div>
                        <!--end alternative-search-form-->
                    </div>
                    <!--end container-->
                </form>
                <!--============ End Hero Form ======================================================================-->
                <div class="background">
                    <!-- <div class="background-image original-size"> -->
                        <!-- <img src="<?= Yii::getAlias('@web') ?>/template/img/hero-background-icons.jpg" alt=""> -->
                    <!-- </div> -->
                    <!--end background-image-->
                </div>
                <!--end background-->
            </div>
            <!--end hero-wrapper-->
        </header>
        <!--end hero-->

    </div>
    <!--end page-->
	<div class="wrapper">
		<!--================= Content Shop ====================-->
		<div class="content-shop">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<!--================= Content Product ====================-->
						<div class="content-product grid masonry">
							<div class="product mask down grid-item grid-item-width1 grid-item-height-mini">
								<div class="images text-center">
									<a href="single-product.html"><img src="http://via.placeholder.com/110x160" alt="product5"></a>
									<div class="button-group">
										<a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
										<a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
									</div><!--button-group-->
								</div><!--images-->
								<div class="info-product">
									<a href="single-product.html" class="title">Серый волк</a>
									<span class="price">бесплатно</span>
								</div><!--info-product-->
							</div><!--product-->
							<div class="product mask down grid-item grid-item-width1 grid-item-height-mini">
								<div class="images text-center">
									<a href="single-product.html"><img src="http://via.placeholder.com/110x160" alt="product5"></a>
									<div class="button-group">
										<a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
										<a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
									</div><!--button-group-->
								</div><!--images-->
								<div class="info-product">
									<a href="single-product.html" class="title">Про кота</a>
									<span class="price">бесплатно</span>
								</div><!--info-product-->
							</div><!--product-->
							<div class="product mask down grid-item grid-item-width1 grid-item-height-mini">
								<div class="images text-center">
									<a href="single-product.html"><img src="http://via.placeholder.com/110x160" alt="product5"></a>
									<div class="button-group">
										<a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
										<a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
									</div><!--button-group-->
								</div><!--images-->
								<div class="info-product">
									<a href="single-product.html" class="title">Руссалочка</a>
									<span class="price">подписка</span>
								</div><!--info-product-->
							</div><!--product-->
							<div class="product mask down grid-item grid-item-width1 grid-item-height-mini">
								<div class="images text-center">
									<a href="single-product.html"><img src="http://via.placeholder.com/110x160" alt="product5"></a>
									<div class="button-group">
										<a href="cart.html" class="custom-btn pink"><i class="fa fa-shopping-bag"></i></a>
										<a href="#" class="custom-btn pink"><i class="fa fa-search"></i></a>
									</div><!--button-group-->
								</div><!--images-->
								<div class="info-product">
									<a href="single-product.html" class="title">Котопес</a>
									<span class="price">бесплатно</span>
								</div><!--info-product-->
							</div><!--product-->
						</div>
						<!--================= End of Content Product ====================-->
						<!--================= Pagination ====================-->
						<ul class="pagination border-top border-color-gray">
							<li class="active"><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li class="no-pointer"><a href="#">...</a></li>
							<li><a href="#">10</a></li>
						</ul><!--pagination-->
						<!--================= End of Pagination ====================-->
					</div>
				</div>
			</div>
		</div>
		<!--===================== Footer ========================-->
		<footer class="bg-yellow">
			<div class="container">
				<!--div class="row">
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="widget-page">
							<h4 class="widget-title">Customer Care</h4>
							<a href="404.html">Register</a>
							<a href="404.html">My Account</a>
							<a href="404.html">Track Order</a>
						</div><!--widget-page-- >
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="widget-page">
							<h4 class="widget-title">FAQ</h4>
							<a href="404.html">Ordering Info</a>
							<a href="404.html">Shipping &amp; Delivery</a>
							<a href="404.html">Our Guarantee</a>
						</div><!--widget-page-- >
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="widget-page">
							<h4 class="widget-title">Our company</h4>
							<a href="404.html">About</a>
							<a href="blog.html">Press</a>
							<a href="single-product.html">Products</a>
						</div><!--widget-page-- >
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="widget-contact">
							<h4 class="widget-title">contact usy</h4>
							<address>123 6th St. Melbourne, FL 32904<br>Phone: (125) 546-4478<br>Email: yesorganic.com</address>
						</div><!--widget-contact-- >
					</div>
				</div-->
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