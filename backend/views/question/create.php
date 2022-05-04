<?php
use backend\widgets\QuestionManageWidget;
/* @var $this yii\web\View */
/* @var $model backend\models\question\CreateRegionQuestion */
/* @var $testModel common\models\StoryTest */
$this->title = 'Создать вопрос';
$this->params['sidebarMenuItems'] = [];
$this->params['breadcrumbs'] = [
    ['label' => 'Тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $testModel,
    'renderData' => $this->render('_form_create', ['model' => $model]),
]) ?>
