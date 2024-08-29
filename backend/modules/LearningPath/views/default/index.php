<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use yii\web\View;
use yii\grid\GridView;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Карты знаний';

$this->registerJs($this->renderFile('@backend/modules/LearningPath/views/default/index.js'));
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2"><?= $this->title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <a href="<?= Url::to(['/learning-path/default/create']); ?>" id="create-learning-path" class="btn btn-primary">Создать запись</a>
        </div>
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'summary' => false,
    'columns' => [
        'name',
        'created_at:datetime',
        'updated_at:datetime',
        [
            'class' => ActionColumn::class,
            'template' => '{update} {delete}',
        ],
    ],
]); ?>
