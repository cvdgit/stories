<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */
?>
<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider
    ]) ?>
</div>
