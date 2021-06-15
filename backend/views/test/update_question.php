<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StoryTestQuestion */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $testModel common\models\StoryTest */
/** @var $errorText string */
$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
?>
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($errorText !== ''): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
            <?= Html::encode($errorText) ?>
        </div>
    <?php endif ?>
    <?= $this->render('_question_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
