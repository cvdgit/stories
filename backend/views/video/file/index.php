<?php

declare(strict_types=1);

use backend\models\SlideVideoSearch;
use backend\models\video\VideoSource;
use backend\widgets\grid\DeleteButton;
use backend\widgets\grid\UpdateButton;
use backend\widgets\grid\ViewButton;
use backend\widgets\WikidsDatePicker;
use common\models\SlideVideo;
use yii\bootstrap\Dropdown;
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
        <a class="btn btn-success" href="<?= Url::to(['video/file/create']); ?>">Добавить видео из файла</a>
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
            [
                'attribute' => 'video_id',
                'label' => 'Имя файла',
            ],
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'datetime',
                'filter' => WikidsDatePicker::widget(['model' => $searchModel, 'attribute' => 'created_at']),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => static function($url, SlideVideo $model): string {
                        return (new ViewButton(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/video/view', 'id' => $model->uuid])))();
                    },
                    'update' => static function($url, $model) {
                        return (new UpdateButton(['video/file/update', 'id' => $model->id]))();
                    },
                    'delete' => static function($url, $model) {
                        return (new DeleteButton(['video/file/delete', 'id' => $model->id]))();
                    }
                ],
            ],
        ],
    ]); ?>
</div>
