<?php
use backend\models\question\QuestionType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\CreateRegionQuestion */
$this->title = 'Создать вопрос';
$this->params['sidebarMenuItems'] = [];
?>
<div class="story-test-question-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="story-test-form">
        <div class="row">
            <div class="col-md-6">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['readonly' => true]) ?>
                <?= $form->field($model, 'image')->fileInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('Создать вопрос', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-6"></div>
        </div>
    </div>
</div>
