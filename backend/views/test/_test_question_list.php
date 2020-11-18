<?php
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/** @var $model common\models\StoryTest */
/** @var $dataProvider yii\data\ActiveDataProvider */
?>
<div>
    <div class="dropdown">
        <button type="button" data-toggle="dropdown" class="btn btn-primary">
            Создать вопрос
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><?= Html::a('По умолчанию', ['test/create-question', 'test_id' => $model->id]) ?></li>
            <li><?= Html::a('Выбор области', ['question/create', 'test_id' => $model->id, 'type' => \backend\models\question\QuestionType::REGION]) ?></li>
        </ul>
    </div>
    <h4>Вопросы теста</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            'name',
            [
                'attribute' => 'answer_number',
                'value' => function($model) {
                    return count($model->storyTestAnswers);
                }
            ],
            [
                'attribute' => 'correct_answer_number',
                'value' => function($model) {
                    return count($model->getCorrectAnswers());
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function($action, $model, $key, $index) {
                    $url = '';
                    if ($action === 'update') {
                        if ($model->typeIsRegion()) {
                            $url = Url::to(['question/update', 'id' => $model->id]);
                        }
                        else {
                            $url = Url::to(['test/update-question', 'question_id' => $model->id]);
                        }
                    }
                    if ($action === 'delete') {
                        $url = Url::to(['test/delete-question', 'question_id' => $model->id]);
                    }
                    return $url;
                },
            ],
        ],
    ]) ?>
</div>