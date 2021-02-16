<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model backend\forms\UpdateWordList */
$this->title = 'Изменить список слов';
$this->params['breadcrumbs'][] = ['label' => 'Test Word Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="test-word-list-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $this->render('_list', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
