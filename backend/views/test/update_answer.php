<?php
use yii\helpers\Html;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTestAnswer */
/** @var $answerImageModel backend\models\AnswerImageUploadForm */
$this->title = 'Изменить ответ';
$this->params['breadcrumbs'] = [
    ['label' => $model->storyQuestion->storyTest->title, 'url' => ['test/update', 'id' => $model->storyQuestion->storyTest->id]],
    ['label' => $model->storyQuestion->name, 'url' => ['test/update-question', 'question_id' => $model->storyQuestion->id]],
];
?>
<div class="story-test-answer-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_answer_form', [
        'model' => $model,
        'answerImageModel' => $answerImageModel,
    ]) ?>
</div>
