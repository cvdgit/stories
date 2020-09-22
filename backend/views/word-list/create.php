<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\TestWordList */
$this->title = 'Создать список слов';
$this->params['breadcrumbs'][] = ['label' => 'Test Word Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-word-list-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>