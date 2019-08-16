<?php

use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $questionModel common\models\StoryTestQuestion */
/** @var $model common\models\StoryTestAnswer */
/** @var $answerImageModel backend\models\AnswerImageUploadForm */

$this->title = $questionModel->name . ' - новый ответ';
?>
<div class="story-test-answer-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_answer_form', [
        'model' => $model,
        'answerImageModel' => $answerImageModel,
    ]) ?>
</div>
