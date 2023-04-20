<?php

declare(strict_types=1);

use backend\Changelog\Changelog;
use backend\helpers\SummaryHelper;
use common\helpers\SmartDate;
use dosamigos\chartjs\ChartJs;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\HtmlPurifier;

/**
 * @var View $this
 * @var array $labels
 * @var array $data
 * @var array $todayStories
 * @var array $users
 * @var int $answersCount
 * @var Changelog[] $changelog
 */

$this->title = 'Панель управления';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-3">
                <h4>Сегодня</h4>
                <ul class="list-group">
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::publishedStories() ?></span> Опубликовано историй</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::registeredUsers() ?></span> Зарегистрировано пользователей</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::commentsWritten() ?></span> Написано комментариев</li>
                    <li class="list-group-item"><span class="badge"><?= SummaryHelper::viewedStories() ?></span> Просмотрено историй</li>
                    <li class="list-group-item"><span class="badge"><?= $answersCount; ?></span> Ответов на вопросы</li>
                </ul>
            </div>
            <div class="col-lg-9">
                <h4>Последние изменения</h4>
                <div style="max-height: 600px; overflow: hidden; overflow-y: auto">
                    <?php foreach ($changelog as $item): ?>
                        <div class="row" style="margin-bottom: 20px">
                            <div class="col-lg-12">
                                <div style="display: flex; flex-direction: row; align-items: end">
                                    <h3 style="margin: 0"><?= $item->getTitle(); ?></h3>
                                    <p class="text-muted" style="margin: 0 0 0 10px" class="time"><?= Yii::$app->formatter->asDate($item->getCreated()) ?></p>
                                </div>
                                <div class="content" style="line-height: 1.4; margin: 10px 0">
                                    <?= HtmlPurifier::process($item->getText()); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div style="height: 400px">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'options' => [

                        ],
                        'clientOptions' => [
                            'maintainAspectRatio' => false,
                            'title' => [
                                'display' => true,
                                'text' => 'Просмотры историй',
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
                            'labels' => $labels,
                            'datasets' => [
                                [
                                    'label' => "Количество просмотров",
                                    'fill' => false,
                                    'borderColor' => 'rgb(75, 192, 192)',
                                    'lineTension' => 0.1,
                                    'data' => $data,
                                ],
                            ]
                        ]
                    ]) ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="table-responsive" style="max-height: 40vh">
                    <table class="table table-condensed table-striped">
                        <caption>Активные сегодня пользователи</caption>
                        <thead>
                        <tr>
                            <th>Пользователь</th>
                            <th>Время</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= Html::encode($user['user_name']) ?></td>
                                <td><?= str_replace('сегодня в ', '', SmartDate::dateSmart($user['user_active_at'], true)) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="table-responsive" style="max-height: 40vh">
                    <table class="table table-condensed table-striped">
                        <caption>Просмотренные сегодня истории (<?= count($todayStories) ?>)</caption>
                        <thead>
                        <tr>
                            <th>История</th>
                            <th>Время</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($todayStories as $story): ?>
                            <tr>
                                <td><?= Html::encode($story['story_title']) ?></td>
                                <td><?= str_replace('сегодня в ', '', SmartDate::dateSmart($story['viewed_at'], true)) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
