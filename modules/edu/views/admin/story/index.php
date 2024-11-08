<?php

declare(strict_types=1);

use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
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
            'format' => 'raw',
            'label' => 'Название истории',
            'value' => static function(array $model): string
            {
                return Html::a($model['title'], Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/story/view', 'alias' => $model['alias']]), ['target' => '_blank']);
            },
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
            'format' => 'raw',
            'label' => 'Обучение'
        ],
    ],
]) ?>
