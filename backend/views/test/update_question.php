<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryTestQuestion */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Вопрос - ' . $model->name;
?>
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_question_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
