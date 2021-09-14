<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StudyTask */
/* @var $assignDataProvider yii\data\ActiveDataProvider */
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Задания', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="study-task-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'assignDataProvider' => $assignDataProvider,
    ]) ?>
</div>
