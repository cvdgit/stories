<?php
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
/* @var View $this */
/* @var ActiveDataProvider $dataProvider */
$this->title = 'Комментарии';
$this->registerCss(<<<CSS
.grid-col {
    word-break: break-word;
}
CSS
);
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'rowOptions' => [
        'class' => 'grid-row',
    ],
    'columns' => [
        'id',
        [
            'attribute' => 'story.title',
            'format' => 'raw',
            'value' => static function($model) {
                return Html::a($model->story->title, Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->story->alias]), ['target' => '_blank']);
            }
        ],
        [
            'label' => 'Пользователь',
            'attribute' => 'user.profileName',
        ],
        [
            'enableSorting' => false,
            'attribute' => 'body',
            'filter' => 'ntext',
            'contentOptions' => ['class' => 'grid-col'],
        ],
        'created_at:datetime',
        [
            'class' => ActionColumn::class,
            'template' => '{delete}',
        ],
    ],
]) ?>
