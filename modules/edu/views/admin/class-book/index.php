<?php

use modules\edu\models\EduClassProgramSearch;
use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Классы';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            'name',
            [
                'attribute' => 'user.profileName',
                'label' => 'Учитель',
            ],
            [
                'attribute' => 'class.name',
                'label' => 'Класс',
            ],
            [
                'attribute' => 'studentCount',
                'label' => 'Кол-во учеников',
            ],
        ],
    ]) ?>
</div>
