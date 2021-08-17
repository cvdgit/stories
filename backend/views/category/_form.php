<?php
use yii\helpers\Html;
use yii\helpers\Url;
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
        <?php if (!$model->isNewRecord): ?>
        <?= Html::button('Истории', ['class' => 'btn btn-primary', 'id' => 'manage-stories']) ?>
        <?php endif ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<div class="modal remote fade" id="manage-stories-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$remote = Url::to(['category/stories', 'category_id' => $model->id]);
$js = <<< JS
(function() {
    "use strict";

    var modal = $('#manage-stories-modal');
    $('#manage-stories').on('click', function() {
        modal.modal({'remote': '$remote'});
    });
    modal.on('hide.bs.modal', function() {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });
})();
JS;
$this->registerJs($js);