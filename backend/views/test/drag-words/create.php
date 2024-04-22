<?php

declare(strict_types=1);

use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use backend\models\drag_words\CreateDragWordsForm;

/**
 * @var StoryTest $quizModel
 * @var CreateDragWordsForm $model
 */

$this->title = 'Новый вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $quizModel->source]],
    ['label' => $quizModel->title, 'url' => ['test/update', 'id' => $quizModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'isCreate' => true,
    'quizModel' => $quizModel,
    'renderData' => $this->render('_question', ['model' => $model, 'isNewRecord' => true]),
]) ?>
