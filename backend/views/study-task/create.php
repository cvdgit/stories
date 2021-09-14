<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StudyTask */
$this->title = 'Новое задание';
$this->params['breadcrumbs'][] = ['label' => 'Задания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="study-task-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
