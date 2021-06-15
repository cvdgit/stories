<?php
use yii\helpers\Html;
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
<div class="story-test-answer-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_answer_form', [
        'model' => $model,
        'answerImageModel' => $answerImageModel,
    ]) ?>
</div>
