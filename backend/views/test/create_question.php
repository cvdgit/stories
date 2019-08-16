<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/** @var $testModel common\models\StoryTest */
/* @var $model common\models\StoryTestQuestion */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $testModel->title . ' - новый вопрос';
?>
<div class="story-test-question-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_question_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
