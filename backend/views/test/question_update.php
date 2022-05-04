<?php
use backend\widgets\QuestionManageWidget;
use yii\bootstrap\Nav;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model backend\models\question\UpdateQuestion */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $testModel common\models\StoryTest */
/* @var common\models\StoryTestQuestion[] $questions */
/** @var $errorText string */
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
    'renderData' => $this->render('_question_form', ['model' => $model, 'dataProvider' => $dataProvider]),
]) ?>
