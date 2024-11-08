<?php

declare(strict_types=1);

use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Истории';
?>
<?= AdminToolbarWidget::widget() ?>

<?= AdminHeaderWidget::widget([
    'title' => $this->title,
    'content' => '',
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'summary' => false,
    'columns' => [
        'id',
        'title',
        'author',
        'publishedAt:datetime',
        'path',
    ],
]) ?>
