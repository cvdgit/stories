<?php

declare(strict_types=1);

use modules\edu\models\EduClass;
use modules\edu\models\EduClassProgramSearch;
use modules\edu\models\EduProgram;
use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var EduClassProgramSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Программы обучения';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <?= AdminHeaderWidget::widget([
        'title' => Html::encode($this->title),
        'content' => Html::a('Создать программу обучения', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']),
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'class.name',
                'label' => 'Класс',
                'filter' => ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                'filterAttribute' => 'class_id',
            ],
            [
                'attribute' => 'program.name',
                'label' => 'Программа',
                'filterAttribute' => 'program_id',
                'filter' => ArrayHelper::map(EduProgram::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'topicsTotal',
                'label' => 'Кол-во тем',
            ],
            [
                'class' => ActionColumn::class,
            ],
        ],
    ]) ?>
</div>
