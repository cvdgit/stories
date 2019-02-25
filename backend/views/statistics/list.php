<?php

use yii\helpers\Html;
use yii\grid\GridView;

use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sidebarMenuItems'] = [
    ['label' => 'История', 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
];
?>
<div class="category-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>Всего просмотров: <?= $model->views_number ?></p>
    <div class="row">
        <div class="col-xs-12">
        <?= ChartJs::widget([
            'type' => 'bar',
            'options' => [
                'height' => 60,
            ],
            'clientOptions' => [
                'title' => [
                    'display' => true,
                    'text' => 'Среднее время просмотра одного слайда',
                ],
                'legend' => [
                    'display' => false,
                ],
                'scales' => [
                    'yAxes' => [
                        ['ticks' => [
                            'beginAtZero' => true,
                        ]]
                    ],
                ],
            ],
            'data' => [
                'labels' => $chartData2['labels'],
                'datasets' => [
                    [
                        'label' => "Среднее время (сек.)",
                        'backgroundColor' => "rgba(54, 162, 235, 0.2)",
                        'borderColor' => "rgb(54, 162, 235)",
                        'borderWidth' => 1,
                        'pointBackgroundColor' => "rgba(179,181,198,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(179,181,198,1)",
                        'data' => $chartData2['data'],
                    ],
                ]
            ]
        ]);
        ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
        <?= ChartJs::widget([
            'type' => 'bar',
            'options' => [
                'height' => 60,
            ],
            'clientOptions' => [
                'title' => [
                    'display' => true,
                    'text' => 'Отношение среднего времи просмотра к количеству слов на слайде',
                ],
                'legend' => [
                    'display' => false,
                ],
                'scales' => [
                    'yAxes' => [
                        ['ticks' => [
                            'beginAtZero' => true,
                        ]]
                    ],
                ],
            ],
            'data' => [
                'labels' => $chartData3['labels'],
                'datasets' => [
                    [
                        'label' => "Отношение среднего времени просмотра к количеству слов",
                        'backgroundColor' => "rgba(54, 162, 235, 0.2)",
                        'borderColor' => "rgb(54, 162, 235)",
                        'borderWidth' => 1,
                        'pointBackgroundColor' => "rgba(179,181,198,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(179,181,198,1)",
                        'data' => $chartData3['data'],
                    ],
                ]
            ]
        ]);
        ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
        <?= ChartJs::widget([
            'type' => 'bar',
            'options' => [
                'height' => 60,
            ],
            'clientOptions' => [
                'title' => [
                    'display' => true,
                    'text' => 'Просмотры слайдов',
                ],
                'legend' => [
                    'display' => false,
                ],
                'scales' => [
                    'yAxes' => [
                        ['ticks' => [
                            'beginAtZero' => true,
                        ]]
                    ],
                ],
            ],
            'data' => [
                'labels' => $chartData['labels'],
                'datasets' => [
                    [
                        'label' => "Просмотров",
                        'backgroundColor' => "rgba(54, 162, 235, 0.2)",
                        'borderColor' => "rgb(54, 162, 235)",
                        'borderWidth' => 1,
                        'pointBackgroundColor' => "rgba(179,181,198,1)",
                        'pointBorderColor' => "#fff",
                        'pointHoverBackgroundColor' => "#fff",
                        'pointHoverBorderColor' => "rgba(179,181,198,1)",
                        'data' => $chartData['data'],
                    ],
                ]
            ]
        ]);
        ?>
        </div>
    </div>

    <?php /* GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            'slide_number',
            [
                'attribute' => 'slide_time',
                'value' => function($model) {
                    return $model->end_time - $model->begin_time;
                }
            ],
            'chars',
            'session',
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'datetime',
            ]
        ],
    ]); */ ?>
</div>
