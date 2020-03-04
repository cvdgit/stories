<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Category;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">
    <?php $form = ActiveForm::begin(['action' => $model->isNewRecord ? ['create'] : ['update', 'id' => $model->id]]); ?>
    <?= $form->field($model, 'parentNode')->dropDownList(Category::getCategoryArray(), ['prompt' => '']) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'sort_field')->dropDownList(['published_at' => 'По дате публикации', 'title' => 'По названию истории', 'episode' => 'По эпизодам'], ['prompt' => 'По умолчанию']) ?>
    <?= $form->field($model, 'sort_order')->dropDownList([SORT_ASC => 'ASC', SORT_DESC => 'DESC'], ['prompt' => 'По умолчанию']) ?>
    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord ? 'Создать категорию' : 'Сохранить изменения'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
