<?php
use backend\widgets\AnswerManageWidget;
/** @var $this yii\web\View */
/** @var $questionModel common\models\StoryTestQuestion */
/** @var $model common\models\StoryTestAnswer */
/** @var $answerImageModel backend\models\AnswerImageUploadForm */
$this->title = 'Создать ответ';
$this->params['breadcrumbs'] = [
    ['label' => $questionModel->storyTest->title, 'url' => ['test/update', 'id' => $questionModel->storyTest->id]],
    ['label' => $questionModel->name, 'url' => ['test/update-question', 'question_id' => $questionModel->id]],
];
?>
<?= AnswerManageWidget::widget([
    'questionModel' => $questionModel,
    'renderData' => $this->render('_answer_form', ['model' => $model, 'answerImageModel' => $answerImageModel]),
]) ?>
