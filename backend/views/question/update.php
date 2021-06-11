<?php
use backend\models\question\QuestionType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\UpdateRegionQuestion */
/** @var $testModel common\models\StoryTest */
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
                    <div class="col-md-6">
                        <div class="image-wrapper">
                            <?= Html::img($model->getImageUrl() . '?t=' . time(), ['width' => '100%', 'data-toggle' => 'modal', 'data-target' => '#regions-modal']) ?>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                <div class="form-group">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?= $this->render('_regions_modal', ['model' => $model]) ?>
<?php
$js = <<< JS
/*
var element = $('#updateregionquestion-regions');
RegionQuestion.init(element.val());
$('#update-region-question-form').on('beforeSubmit', function() {
    element.val(RegionQuestion.getRegionsJson());
    return true;
});
RegionQuestion.addEventListener('onDeleteRegion', function(args) {
    if (args.answerID) {
        $.get('/admin/index.php?r=question/delete-answer', {'id': args.answerID})
            .done(function(response) {
                $('#update-region-question-form').submit();
            });
    }
});*/
JS;
//$this->registerJs($js);
