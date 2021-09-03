<?php
use backend\widgets\WikidsDatePicker;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Nav;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;
/** @var $searchModel backend\models\SlideVideoSearch */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $source int */
$this->title = 'Видео';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="dropdown" style="margin-bottom: 20px">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-success">Добавить <b class="caret"></b></a>
        <?= Dropdown::widget([
            'items' => [
                ['label' => 'Видео YouTube', 'url' => ['video/create']],
                ['label' => 'Видео из файла', 'url' => ['video/file/create']],
            ],
        ]) ?>
    </div>
    <?= Nav::widget([
        'options' => ['class' => 'nav nav-tabs material-tabs'],
        'items' => \backend\models\video\VideoSource::asNavItems($source),
    ]) ?>
    <?php
    $columns = [
        ['class' => SerialColumn::class],
        'title',
    ];
    if ($searchModel->sourceIsYouTube()) {
        $columns[] = 'video_id';
    }
    if ($searchModel->sourceIsFile()) {
        $columns[] = [
            'attribute' => 'video_id',
            'label' => 'Имя файла',
        ];
    }
    $columns[] = [
        'attribute' => 'created_at',
        'value' => 'created_at',
        'format' => 'datetime',
        'filter' => WikidsDatePicker::widget(['model' => $searchModel, 'attribute' => 'created_at']),
    ];
    if ($searchModel->sourceIsYouTube()) {
        $columns[] = [
            'attribute' => 'status',
            'value' => static function(\common\models\SlideVideo $model) {
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
        ];
    }

    $actionColumn = [
        'class' => ActionColumn::class,
        'buttons' => [
            'view' => function($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-eye-open"></span>',
                    'https://www.youtube.com/watch?v=' . $model->video_id,
                    ['target' => '_blank']);
            }
        ],
    ];

    if ($searchModel->sourceIsFile()) {
        $actionColumn['template'] = '{update} {delete}';
        $actionColumn['buttons'] = [
            'update' => static function($url, $model) {
                return (new \backend\widgets\grid\UpdateButton(['video/file/update', 'id' => $model->id]))();
            },
            'delete' => static function($url, $model) {
                return (new \backend\widgets\grid\DeleteButton(['video/file/delete', 'id' => $model->id]))();
            }
        ];
    }

    $columns[] = $actionColumn;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ])
    ?>
</div>
