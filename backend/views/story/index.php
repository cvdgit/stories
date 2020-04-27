<?php

use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\UserHelper;
use common\models\Category;
use common\models\Story;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;

/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $searchModel backend\models\StorySearch */
/** @var $batchForm backend\models\StoryBatchCommandForm */

$this->title = 'Управление историями';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать историю', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?php Pjax::begin(['id' => 'pjax-stories']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        // ['class' => CheckboxColumn::class],
        'id',
        'title',
        [
            'format' => 'raw',
            'attribute' => 'mode',
            'value' => function($model) {
                $mode = '';
                if ($model->isAudioStory()) {
                    $mode = '<i class="glyphicon glyphicon-volume-up" data-toggle="popover" title="Озвучено" style="font-size: 20px; color: #d9534f"></i>';
                }
                return $mode;
            }
        ],
        [
            'attribute' => 'user_id',
            'value' => 'author.username',
            'filter' => UserHelper::getUserArray(),
        ],
        [
            'attribute' => 'story_categories',
            'value' => function($model) {
                return implode(', ', array_map(function($item){
                    return $item->name;
                }, $model->categories));
            }
        ],
        [
            'attribute' => 'created_at',
            'value' => 'created_at',
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'created_at',
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]),
        ],
        [
            'attribute' => 'updated_at',
            'value' => 'updated_at',
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated_at',
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]),
        ],
        [
            'attribute' => 'status',
            'value' => function($model) {
                return $model->getStatusText();
            },
            'filter' => Story::getStatusArray(),
        ],
        [
            'attribute' => 'sub_access',
            'value' => function($model) {
                return $model->getSubAccessText();
            },
            'filter' => Story::getSubAccessArray(),
        ],
        'views_number',
        [
            'class' => ActionColumn::class,
            'buttons' => [
                'view' => function($url, $model) {
                    return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias]),
                            ['target' => '_blank', 'data-pjax' => 0]);
                }
            ],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>
