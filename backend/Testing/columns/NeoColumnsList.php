<?php

declare(strict_types=1);

namespace backend\Testing\columns;

use backend\Testing\TestSearch;
use common\helpers\UserHelper;
use common\models\StoryTest;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;

class NeoColumnsList implements ColumnListInterface
{
    private $searchModel;

    public function __construct(TestSearch $searchModel)
    {
        $this->searchModel = $searchModel;
    }

    public function getList(): array
    {
        return [
            [
                'attribute' =>'title',
                'format' => 'raw',
                'value' => static function(StoryTest $model) {
                    return Html::a($model->title, ['test/update', 'id' => $model->id], ['title' => 'Перейти к редактированию']);
                },
            ],
            [
                'attribute' => 'created_by',
                'value' => 'createdBy.profileName',
                'label' => 'Автор',
                'filter' => UserHelper::getTestCreatorsUserArray(),
            ],
            [
                'attribute' => 'childrenTestsCount',
                'label' => 'Количество вариантов',
            ],
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'datetime',
                'filter' => DatePicker::widget([
                    'model' => $this->searchModel,
                    'attribute' => 'created_at',
                    'language' => 'ru',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => (new ActionUrlCreator())(),
            ],
        ];
    }
}
