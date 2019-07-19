<?php

use backend\helpers\SummaryHelper;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;
use yii\web\JsExpression;

/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Панель управления';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-xs-6">
                <h4>Сегодня</h4>
                <ul class="list-group">
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::activatedSubscriptions() ?></span> Активировано подписок</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::publishedStories() ?></span> Опубликовано историй</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::registeredUsers() ?></span> Зарегистрировано пользователей</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::commentsWritten() ?></span> Написано комментариев</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::viewedStories() ?></span> Просмотрено историй</li>
                </ul>
            </div>
        	<div class="col-xs-6">
        		<h4>Количество просмотров историй с % завершения</h4>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => false,
                    'columns' => [
                        [
                            'attribute' => 'title',
                            'label' => 'История',
                        ],
                        [
                            'attribute' => 'views_number',
                            'label' => 'Количество просмотров',
                        ],
                        [
                            'attribute' => 'story_done',
                            'label' => '% завершенных просмотров',
                        ],
                    ],
                ]) ?>
        	</div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?= ChartJs::widget([
                    'type' => 'line',
                    'options' => [
                    ],
                    'clientOptions' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Количество просмотров историй по дням',
                        ],
                        'legend' => [
                            'display' => false,
                        ],
                        'scales' => [
                            'yAxes' => [
                                ['ticks' => [
                                    'beginAtZero' => true,
                                    'stepSize' => 1,
                                ]]
                            ],
                        ],
                    ],
                    'data' => [
                        'labels' => $storyViewsData['labels'],
                        'datasets' => [
                            [
                                'label' => "Количество просмотров",
                                'fill' => false,
                                'borderColor' => 'rgb(75, 192, 192)',
                                'lineTension' => 0.1,
                                'data' => $storyViewsData['data'],
                            ],
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
