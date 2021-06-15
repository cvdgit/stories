<?php
use backend\models\question\QuestionType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\UpdateRegionQuestion */
/** @var $testModel common\models\StoryTest */
/** @var $errorText string */
$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Тесты', 'url' => ['test/index', 'source' => $testModel->source]],
    ['label' => $testModel->title, 'url' => ['test/update', 'id' => $testModel->id]],
    $this->title,
];
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
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($errorText !== ''): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
            <?= Html::encode($errorText) ?>
        </div>
    <?php endif ?>
    <div class="story-test-form">
        <?php $form = ActiveForm::begin(['id' => 'update-region-question-form']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['readonly' => true]) ?>
                <?= $form->field($model, 'imageFile')->fileInput() ?>
                <?= $form->field($model, 'regions')->hiddenInput()->label(false) ?>
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
                <div class="form-group">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Копировать вопрос', ['question/copy', 'id' => $model->getModelID()], ['class' => 'btn btn-default', 'style' => 'margin-left: 20px']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?= $this->render('_regions_modal', ['model' => $model]) ?>
