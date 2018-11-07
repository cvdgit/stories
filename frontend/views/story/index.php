<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\StorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Каталог историй';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-shop">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12 col-3">
                <aside class="sidebar-shop">
                    <div class="widget-search">
                        <h3 class="widget-title">Search</h3>
                        <form>
                            <div class="form-group">
                                <input type="text" placeholder="Search...">
                                <button><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="widget-category">
                        <h3 class="widget-title">Category</h3>
                        <ul>
                            <li class="active widget-category-hover"><a href="#" class="text-black">All</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Fresh Fruit</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Herbs</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Fresh Meat</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Sea food</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Seed</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Spices</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Vegetable</a></li>
                            <li class="widget-category-hover"><a class="text-black" href="single-product.html">Milk</a></li>
                        </ul>
                    </div>
                </aside>
            </div>
            <?= ListView::widget([
                'layout' => '<div class="filter-wrap">{summary}</div><div class="content-product three-column with-sidebar">{items}</div>{pager}',
                'options' => ['class' => 'col-md-9 col-sm-9 col-xs-12 col-9'],
                'summary' => '<p>Showing {begin}–{end} of {totalCount} results</p>',
                'dataProvider' => $dataProvider,
                'itemOptions' => ['tag' => false],
                'itemView' => '_storyitem',
            ]) ?>
        </div>
    </div>
</div>