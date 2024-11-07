<?php

declare(strict_types=1);

use backend\Changelog\Changelog;
use backend\helpers\SummaryHelper;
use common\helpers\SmartDate;
use dosamigos\chartjs\ChartJs;
use yii\helpers\Html;
use yii\helpers\Url;
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
$this->registerJs($this->renderFile('@backend/views/site/index.js'));
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
                <div id="changelogs" style="min-height: 250px; max-height: 600px; overflow-y: auto; display: flex; flex-direction: column; row-gap: 20px">
                    <?php foreach ($changelog as $item): ?>
                        <a class="changelog-item" href="<?= Url::to(['/changelog/default/view', 'id' => $item->getId()]) ?>" style="display: flex; flex-direction: row; justify-content: space-between">
                            <h4 style="margin: 0"><?= Html::encode($item->getTitle()) ?></h4>
                            <div style="display: flex; flex-direction: row; align-items: center">
                                <?php if ($item->isNew()): ?>
                                <span class="label label-info" style="margin-right: 10px">Новое</span>
                                <?php endif ?>
                                <p class="text-muted" class="time" style="margin: 0"><?= SmartDate::dateSmart($item->getCreated()->getTimestamp()) ?></p>
                            </div>
                        </a>
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

<div class="modal modal-fullscreen remote" id="changelog-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
