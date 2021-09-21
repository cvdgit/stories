<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\study_task\CreateStudyTaskForm */
$this->title = 'Новое задание';
$this->params['breadcrumbs'][] = ['label' => 'Задания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-task-create">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-7">
            <div class="study-task-form">
                <?php $form = ActiveForm::begin(['id' => 'study-task-form']); ?>
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                <?= $form->field($model, 'status')->dropDownList($model->getStudyTaskStatusesAsArray(), ['disabled' => true]) ?>
                <?= $this->render('_select_story', [
                    'form' => $form,
                    'model' => $model,
                    'isTaskStory' => false,
                ]) ?>
                <div class="form-group form-group-controls">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>