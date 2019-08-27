<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Результаты ответов на вопросы';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        'id',
        'question.name',
        'user.username',
        'story.title',
        [
            'attribute' => 'answer_is_correct',
            'value' => function($row) {
                return $row->isCorrect() ? 'Да' : 'Нет';
            }
        ],
        'created_at:datetime',
    ],
]) ?>
