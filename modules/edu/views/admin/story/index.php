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
        [
            'attribute' => 'id',
            'label' => 'ИД',
        ],
        [
            'attribute' => 'title',
            'label' => 'Название истории',
        ],
        [
            'attribute' => 'author',
            'label' => 'Автор',
        ],
        [
            'attribute' => 'publishedAt',
            'format' => 'datetime',
            'label' => 'Опубликована',
        ],
        [
            'attribute' => 'path',
            'label' => 'Обучение'
        ],
    ],
]) ?>
