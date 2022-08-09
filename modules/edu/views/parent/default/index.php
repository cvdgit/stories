<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Мои ученики';
?>
<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            'name',
            [
                'attribute' => 'studentLogin.username',
                'label' => 'Логин',
            ],
            [
                'label' => 'Учеников',
                'attribute' => 'studentLogin.password',
            ],
        ],
    ]) ?>

    <div style="margin-bottom: 40px">
        <?= Html::a('Добавить ученика', ['/edu/parent/default/create-student'], ['class' => 'btn btn-small']) ?>
    </div>
</div>
