<?php
use backend\widgets\QuestionManageWidget;
/* @var $this yii\web\View */
/* @var $model backend\models\question\UpdateRegionQuestion */
/** @var $testModel common\models\StoryTest */
$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $testModel,
    'currentModelId' => $model->getModelID(),
    'renderData' => $this->render('_form_update', ['model' => $model]),
]) ?>
