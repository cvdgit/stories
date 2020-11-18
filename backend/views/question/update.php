<?php
use backend\assets\TestQuestionAsset;
use backend\models\question\QuestionType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\question\UpdateRegionQuestion */
$this->title = 'Изменить вопрос';
$this->params['sidebarMenuItems'] = [];
TestQuestionAsset::register($this);
?>
<div class="story-test-question-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="story-test-form">
        <div class="row">
            <div class="col-md-6">
                <?php $form = ActiveForm::begin(['id' => 'update-region-question-form']); ?>
                <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['readonly' => true]) ?>
                <?= $form->field($model, 'imageFile')->fileInput() ?>
                <?= $form->field($model, 'regions')->hiddenInput()->label(false) ?>
                <?php if ($model->hasImage()): ?>
                <div id="image-region" style="position: relative; height: 480px; width: 640px; margin-bottom: 40px">
                    <div style="position: absolute; left: 0; top: 0">
                        <?= Html::img($model->getImageUrl(), ['width' => '100%', 'height' => '100%']) ?>
                    </div>
                </div>
                <?php endif ?>
                <div class="form-group">
                    <?= Html::submitButton('Изменить вопрос', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Создать ответы', ['question/create-answers', 'question_id' => $model->getModelID()], ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-6">
                <div id="region-table"></div>
            </div>
        </div>
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
JS;
$this->registerJs($js);
