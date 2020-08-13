<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryTestQuestion */
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->params['sidebarMenuItems'] = [
    ['label' => $model->storyTest->title, 'url' => ['test/update', 'id' => $model->storyTest->id]],
];
$this->title = 'Изменить вопрос';
?>
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_question_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
