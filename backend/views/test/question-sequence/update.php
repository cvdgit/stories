<?php
use backend\models\question\sequence\SequenceAnswerForm;
use backend\models\question\sequence\UpdateSequenceQuestion;
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use yii\web\View;
/** @var View $this */
/** @var UpdateSequenceQuestion $model */
/** @var StoryTest $testModel */
/** @var string $errorText */
/** @var SequenceAnswerForm $createAnswerModel */
$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $testModel,
    'currentModelId' => $model->getModel()->id,
    'renderData' => $this->render('_form_update', ['model' => $model, 'createAnswerModel' => $createAnswerModel]),
]) ?>
