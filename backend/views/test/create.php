<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StoryTest */
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Создать тест';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Все тесты', 'url' => ['test/index']],
];
?>
<div class="story-test-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
