<?php
use backend\assets\DmFileUploaderAsset;
use backend\assets\SortableJsAsset;
use backend\models\question\QuestionType;
use backend\models\question\sequence\SequenceAnswerForm;
use backend\widgets\QuestionManageWidget;
use backend\widgets\QuestionSlidesWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\sequence\UpdateSequenceQuestion */
/* @var $testModel common\models\StoryTest */
/** @var $errorText string */
$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $testModel,
    'currentModelId' => $model->getModel()->id,
    'renderData' => $this->render('_form_update', ['model' => $model]),
]) ?>
