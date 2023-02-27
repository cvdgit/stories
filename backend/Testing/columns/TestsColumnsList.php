<?php

declare(strict_types=1);

namespace backend\Testing\columns;

use backend\Testing\TestSearch;
use common\helpers\UserHelper;
use common\models\StoryTest;
use common\models\test\AnswerType;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;

class TestsColumnsList implements ColumnListInterface
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
                    return Html::a($model->title, ['test/update', 'id' => $model->id], ['data-pjax' => '0', 'title' => 'Перейти к редактированию']);
                },
            ],
            [
                'attribute' => 'created_by',
                'value' => 'createdBy.profileName',
                'label' => 'Автор',
                'filter' => UserHelper::getTestCreatorsUserArray(),
            ],
            [
                'attribute' => 'relatedTestsNumber',
                'label' => 'Количество тестов',
            ],
            [
                'attribute' => 'answer_type',
                'value' => static function($model) {
                    return AnswerType::asText($model->answer_type);
                },
                'filter' => AnswerType::asArray(),
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
                'attribute' => 'transition',
                'label' => 'Переход',
                'format' => 'raw',
                'value' => (new TransitionColumnValue())(),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => (new ActionUrlCreator())(),
            ],
        ];
    }
}
