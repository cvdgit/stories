<?php

declare(strict_types=1);

namespace backend\Testing\columns;

use backend\Testing\TestSearch;
use common\helpers\UserHelper;
use common\models\StoryTest;
use common\models\test\AnswerType;
use dosamigos\datepicker\DatePicker;
use Yii;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;

class DefaultColumnsList implements ColumnListInterface
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
                'attribute' => 'questionsNumber',
                'label' => 'Вопросов',
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
                    ],
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
                'template' => '{all}',
                'buttons' => [
                'all' => static function ($url, $model, $key) {
                    return '<div class="dropdown">
    <button type="button" data-toggle="dropdown" class="grid-actions-btn">
    <svg viewBox="0 0 16 4" width="16" height="4" class="i" focusable="false"><title>More</title><desc>Three dots next to one another</desc><path d="M8,0 C9.105,0 10,0.895 10,2 C10,3.105 9.105,4 8,4 C6.895,4 6,3.105 6,2 C6,0.895 6.895,0 8,0"></path><path d="M2,0 C3.105,0 4,0.895 4,2 C4,3.105 3.105,4 2,4 C0.895,4 0,3.105 0,2 C0,0.895 0.895,0 2,0"></path><path d="M14,0 C15.105,0 16,0.895 16,2 C16,3.105 15.105,4 14,4 C12.895,4 12,3.105 12,2 C12,0.895 12.895,0 14,0"></path></svg>
</button>'
                        .\yii\bootstrap\Dropdown::widget([
                            'encodeLabels' => false,
                            'items' => [
                                ['label' => 'Ссылка для getCourse', 'encode' => false, 'linkOptions' => ['data-pjax' => '0', 'target' => '_blank'], 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/getcourse/quiz', 'id' => $model->id, 'get_course_id' => ''])],
                                ['label' => 'Изменить', 'url' => ActionUrlCreator::updateUrl($model)],
                                ['label' => 'Удалить', 'url' => ActionUrlCreator::deleteUrl($model)],
                            ],
                            'options' => ['class' => 'pull-right']
                        ]).'</div>';
                }],
                'urlCreator' => (new ActionUrlCreator())(),
            ],
        ];
    }
}
