<?php
use backend\models\question\QuestionType;
use backend\widgets\QuestionErrorTextWidget;
use backend\widgets\QuestionSlidesWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\UpdateRegionQuestion */
/** @var $testModel common\models\StoryTest */
/** @var $errorText string */
$css = <<< CSS
.image-wrapper {
    background-color: #f5f5f5;
    padding: 10px;
}
.image-wrapper img:hover {
    cursor: pointer;
}
.story-test-question-update .form-group {
    margin-top: 20px;
}
CSS;
$this->registerCss($css);
?>
<div class="story-test-form">
    <?= QuestionErrorTextWidget::widget(['questionModel' => $model->getModel()]) ?>
    <?php $form = ActiveForm::begin(['id' => 'update-region-question-form']); ?>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['readonly' => true]) ?>
            <?= $form->field($model, 'imageFile')->fileInput() ?>
            <?= $form->field($model, 'regions')->hiddenInput()->label(false) ?>
            <?= QuestionSlidesWidget::widget(['model' => $model->getModel()]) ?>
            <?php if ($model->hasImage()): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert" style="font-size:1.5rem;margin-bottom:2px">Кликните на изображение для перехода к редактированию областей</div>
                    </div>
                    <div class="col-md-6">
                        <div class="image-wrapper">
                            <?= Html::img($model->getImageUrl() . '?t=' . time(), ['width' => '100%', 'data-toggle' => 'modal', 'data-target' => '#regions-modal']) ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group form-group-controls">
                <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Копировать вопрос', ['question/copy', 'id' => $model->getModelID()], ['class' => 'btn btn-default', 'style' => 'margin-left: 20px']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?= $this->render('_regions_modal', ['model' => $model]) ?>
