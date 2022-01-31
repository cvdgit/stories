<?php
use backend\models\question\QuestionType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\text\CreateTextVariantForm */
/* @var $testModel common\models\StoryTest */
$this->title = 'Создать вопросы';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
?>
<div class="story-test-question-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="story-test-form">
        <div class="row">
            <div class="col-md-6">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'text')->textarea(['rows' => 20]) ?>
                <div class="form-group form-group-controls">
                    <?= Html::submitButton('Создать вопросы', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-6"></div>
        </div>
    </div>
</div>
