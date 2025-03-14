<?php

declare(strict_types=1);

use yii\bootstrap\Nav;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var array $items
 * @var string $view
 * @var array $viewParams
 * @var View $this
 */

$title = 'Прогресс обучения';
$this->setMetaTags($title,
    $title,
    '',
    $title);
$this->registerCss(<<<CSS
.filter__wrap {
    padding: 2rem 0;
}
.filter-arrow--left {
    text-align: left;
}
.filter-arrow--right {
    text-align: right;
}
.filter-arrow__link {
    display: inline-block;
}
.filter-arrow__link i {
    font-size: 3rem;
    line-height: 3rem;
}
.history-nav {
    margin-bottom: 20px;
}
CSS
);
?>
<div>
    <h1>История <span>обучения</span></h1>
    <div class="history-nav">
    <?php Pjax::begin(['id' => 'pjax-students']) ?>
        <?= Nav::widget([
            'options' => ['class' => 'nav nav-tabs material-tabs'],
            'items' => $items,
        ]) ?>
    <?php Pjax::end() ?>
    </div>
    <div class="history-content">
        <?= $this->render($view, $viewParams) ?>
    </div>
</div>
