<?php
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use backend\models\pass_test\CreatePassTestForm;
/** @var StoryTest $quizModel */
/** @var CreatePassTestForm $model */
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
