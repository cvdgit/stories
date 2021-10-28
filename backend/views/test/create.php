<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StoryTest */
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Создать тест';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $model->source]],
    $this->title,
];
?>
<div class="story-test-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]) ?>
        </div>
    </div>
</div>