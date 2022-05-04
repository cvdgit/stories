<?php
use backend\widgets\AnswerManageWidget;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTestAnswer */
/** @var $answerImageModel backend\models\AnswerImageUploadForm */
$this->title = 'Изменить ответ';
$this->params['breadcrumbs'] = [
    ['label' => $model->storyQuestion->storyTest->title, 'url' => ['test/update', 'id' => $model->storyQuestion->storyTest->id]],
    ['label' => $model->storyQuestion->name, 'url' => ['test/update-question', 'question_id' => $model->storyQuestion->id]],
];
?>
<?= AnswerManageWidget::widget([
    'questionModel' => $model->storyQuestion,
    'currentModelId' => $model->id,
    'renderData' => $this->render('_answer_form', ['model' => $model, 'answerImageModel' => $answerImageModel]),
]) ?>
