<?php
use backend\models\question\QuestionType;
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
            <li><?= Html::a('Выбор области', ['question/create', 'test_id' => $model->id, 'type' => QuestionType::REGION]) ?></li>
            <li><?= Html::a('Последовательность', ['test/question-sequence/create', 'test_id' => $model->id]) ?></li>
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
                        $route = ['test/update-question', 'question_id' => $model->id];
                        if ($model->typeIsRegion()) {
                            $route = ['question/update', 'id' => $model->id];
                        }
                        if ($model->typeIsSequence()) {
                            $route = ['test/question-sequence/update', 'id' => $model->id];
                        }
                        $url = Url::to($route);
                    }
                    if ($action === 'delete') {
                        $route = ['test/delete-question', 'question_id' => $model->id];
                        if ($model->typeIsRegion()) {
                            $route = ['question/delete', 'id' => $model->id];
                        }
                        if ($model->typeIsSequence()) {
                            $route = ['test/question-sequence/delete', 'id' => $model->id];
                        }
                        $url = Url::to($route);
                    }
                    return $url;
                },
            ],
        ],
    ]) ?>
</div>