<?php
use backend\models\question\QuestionType;
use backend\models\question\sequence\SequenceAnswerForm;
use backend\models\question\sequence\UpdateSequenceQuestion;
use backend\widgets\QuestionErrorTextWidget;
use backend\widgets\QuestionSlidesWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
/** @var View $this */
/** @var UpdateSequenceQuestion $model */
/** @var SequenceAnswerForm $createAnswerModel */
?>
<div class="story-test-question-update">
    <?= QuestionErrorTextWidget::widget(['questionModel' => $model->getModel()]) ?>
    <div class="story-test-form">
        <div class="row">
            <div class="col-lg-6">
                <?= $this->render('_create_answer_form', ['model' => $createAnswerModel, 'questionUpdateModel' => $model]) ?>
            </div>
            <div class="col-lg-6">
                <?php $form = ActiveForm::begin(['id' => 'update-sequence-question-form']); ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['disabled' => true]) ?>
                <?= $form->field($model, 'sort_view')->dropDownList($model->getSortViewValues()) ?>
                <?= $form->field($model, 'story_test_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'sortable')->hiddenInput()->label(false) ?>
                <?= QuestionSlidesWidget::widget(['model' => $model->getModel()]) ?>
                <div class="form-group form-group-controls">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
