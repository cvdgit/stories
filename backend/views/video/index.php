<?php

declare(strict_types=1);

use backend\models\SlideVideoSearch;
use backend\models\video\VideoSource;
use backend\widgets\WikidsDatePicker;
use common\models\SlideVideo;
use yii\bootstrap\Nav;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var SlideVideoSearch $searchModel
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Видео';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div style="margin: 20px 0">
        <a class="btn btn-success" href="<?= Url::to(['video/create']); ?>">Добавить видео YouTube</a>
    </div>
    <?= Nav::widget([
        'options' => ['class' => 'nav nav-tabs material-tabs'],
        'items' => VideoSource::asNavItems(),
    ]); ?>
</div>
<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            'title',
            'video_id',
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'datetime',
                'filter' => WikidsDatePicker::widget(['model' => $searchModel, 'attribute' => 'created_at']),
            ],
            [
                'attribute' => 'status',
                'value' => static function(SlideVideo $model) {
                    $className = 'ok';
                    $color = '#5cb85c';
                    if (!$model->isSuccess()) {
                        $className = 'remove';
                        $color = '#a94442';
                    }
                    return Html::tag('i', '', ['class' => "glyphicon glyphicon-$className", 'style' => "color: $color"]);
                },
                'format' => 'raw',
                'filter' => ['Успешно', 'Ошибка'],
            ],
            [
                'class' => ActionColumn::class,
                'buttons' => [
                    'view' => static function($url, $model): string {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            'https://www.youtube.com/watch?v=' . $model->video_id,
                            ['target' => '_blank']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
