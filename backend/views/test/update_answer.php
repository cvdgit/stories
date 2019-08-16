<?php

use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $model common\models\StoryTestAnswer */
/** @var $answerImageModel backend\models\AnswerImageUploadForm */

$this->title = $model->storyQuestion->name;
?>
<div class="story-test-answer-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_answer_form', [
        'model' => $model,
        'answerImageModel' => $answerImageModel,
    ]) ?>
</div>
