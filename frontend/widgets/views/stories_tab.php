<?php
use yii\helpers\Html;
/** @var $categories common\models\Category[] */
/** @var $stories common\models\Story[] */
/** @var $prefix string */
?>
<div class="stories">
    <div class="stories-label">
        <p class="stories-label__text">Выберите категорию</p>
    </div>
    <div class="categories__wrap" role="tablist">
        <ul class="categories">
            <?php foreach ($categories as $i => $category): ?>
            <li class="categories-item<?= $i === 0 ? ' active' : '' ?>">
                <?= Html::a($category->name, '#' . $prefix . $category->alias, [
                    'class' => 'categories-item__link nav-link' . ($i === 0 ? ' active' : ''),
                    'data-toggle' => 'tab',
                    'role' => 'tab'
                ]) ?>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="stories-content">
        <div class="stories-content__list tab-content">
            <?php foreach ($categories as $i => $category): ?>
            <div class="content-items tab-pane<?= $i === 0 ? ' active' : '' ?>" role="tabpanel" id="<?= $prefix . $category->alias ?>">
                <div class="row">
                    <?php foreach ($stories[$category->alias] as $story): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="content-item">
                            <a class="content-item__link" href="<?= $story->getStoryUrl() ?>">
                                <div class="content-item-image">
                                    <div class="content-item-image__overlay">
                                        <span></span>
                                    </div>
                                    <img class="content-item-image__image" src="<?= $story->getListThumbPath() ?>" alt="">
                                </div>
                                <div class="content-item-caption">
                                    <p class="content-item-caption__flex"></p>
                                    <p></p>
                                    <h3 class="content-item-caption__name"><?= $story->title ?></h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
            <?php endforeach ?>
        </div>
        <div class="stories-content__more">
            <?= Html::a('Все категории', ['story/index', 'section' => 'stories'], ['class' => 'button']) ?>
        </div>
    </div>
</div>