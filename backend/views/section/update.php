<?php
use common\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\section\UpdateSectionForm */
$this->title = 'Раздел - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Разделы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6">
        <div class="section-create">
            <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
            <div class="section-form">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'category_id')->dropDownList(Category::getTreeArray(), ['prompt' => 'Пусто']) ?>
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'h1')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
                <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'visible')->checkbox() ?>
                <div class="form-group">
                    <?= Html::submitButton(($model->isNewRecord ? 'Создать раздел' : 'Сохранить изменения'), ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
