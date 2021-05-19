<?php
use backend\assets\TestQuestionAsset;
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
TestQuestionAsset::register($this);
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
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if ($model->hasImage()): ?>
                    <div id="image-region" style="position: relative; margin-bottom: 40px; width: <?= $model->getImageWidth() ?>px; height: <?= $model->getImageHeight() ?>px">
                        <div style="position: absolute; left: 0; top: 0">
                            <?= Html::img($model->getImageUrl() . '?t=' . time(), ['width' => '100%', 'height' => '100%']) ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div id="region-table"></div>
                <div class="form-group">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$js = <<< JS
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
});
JS;
$this->registerJs($js);
