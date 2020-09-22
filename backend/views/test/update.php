<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\StoryTest */
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Изменить тест';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Все тесты', 'url' => ['test/index']],
];
?>
<div class="story-test-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]) ?>
        </div>
        <div class="col-md-6">
            <?php if (!$model->isNewRecord): ?>
                <?php if ($model->isRemote()): ?>
                    <?= $this->render('_test_children_list', ['model' => $model]) ?>
                <?php endif ?>
                <?php if ($model->isSourceTest()): ?>
                    <?= $this->render('_test_question_list', ['model' => $model, 'dataProvider' => $dataProvider]) ?>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</div>