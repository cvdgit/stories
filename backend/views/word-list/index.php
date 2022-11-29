<?php

declare(strict_types=1);

use common\models\TestWordList;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use backend\models\TestWordListSearch;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var TestWordListSearch $searchModel
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Списки слов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-word-list-index">
    <h1><?= Html::encode($this->title); ?></h1>
    <p>
        <?= Html::a('Создать список слов', ['create'], ['class' => 'btn btn-success']); ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            [
                'class' => SerialColumn::class,
            ],
            'name',
            [
                'attribute' => 'story_id',
                'label' => 'История',
                'value' => static function(TestWordList $model) {
                    if (count($model->stories) > 0) {
                        return $model->stories[0]->title;
                    }
                    return '';
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'datetime',
                'filterInputOptions' => [
                    'type' => 'date',
                    'class' => 'form-control',
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
