<?php

use backend\widgets\StudyTaskAssignWidget;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\study_task\UpdateStudyTaskForm */
/* @var $assignDataProvider yii\data\ActiveDataProvider */
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Задания', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="study-task-update">
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
                    'isTaskStory' => $model->isTaskStory(),
                ]) ?>
                <div class="form-group form-group-controls">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-md-5">
            <div style="margin-bottom:10px">
                <?= StudyTaskAssignWidget::widget([
                    'studyTaskID' => $model->id,
                ]) ?>
            </div>
            <?= GridView::widget([
                'dataProvider' => $assignDataProvider,
                'columns' => [
                    'name',
                ],
            ]) ?>
        </div>
    </div>
</div>