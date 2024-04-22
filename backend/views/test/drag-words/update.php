<?php

declare(strict_types=1);

use backend\models\drag_words\UpdateDragWordsForm;
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use common\models\StoryTestQuestion;

/**
 * @var StoryTest $quizModel
 * @var UpdateDragWordsForm $model
 * @var StoryTestQuestion $questionModel
 */

$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $quizModel->source]],
    ['label' => $quizModel->title, 'url' => ['test/update', 'id' => $quizModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $quizModel,
    'currentModelId' => $questionModel->id,
    'renderData' => $this->render('_question', ['model' => $model, 'isNewRecord' => false]),
]) ?>
