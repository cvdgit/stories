<?php
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/** @var $model common\models\StoryTest */
/** @var $dataProvider yii\data\ActiveDataProvider */
?>
<div>
    <p>
        <?= Html::a('Создать вопрос', ['test/create-question', 'test_id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
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
                        $url = Url::to(['test/update-question', 'question_id' => $model->id]);
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