<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Category;
/* @var $this yii\web\View */
/* @var $model backend\models\category\BaseCategoryForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $category Category */
?>
<div class="category-form">
    <?php if (!$model->isNewRecord()): ?>
    <div class="row" style="margin-bottom:10px">
        <div class="col-md-8">
            <?= Html::a('Создать категорию', ['create', 'tree' => $model->tree, 'parent_id' => $category->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::button('Истории', ['class' => 'btn btn-primary', 'id' => 'manage-stories']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->getModelID()], ['class' => 'btn btn-danger', 'id' => 'delete-category']) ?>
        </div>
        <div class="col-md-4">
            <div class="dropdown pull-right">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default">Переместить в дерево <b class="caret"></b></a>
                <?= \yii\bootstrap\Dropdown::widget([
                    'items' => $treeItems,
                ]) ?>
            </div>
        </div>
    </div>
    <?php endif ?>
    <?php $form = ActiveForm::begin(['action' => $model->isNewRecord() ? ['create', 'tree' => $model->tree] : ['update-ajax', 'id' => $model->getModelID()]]); ?>
    <?= $form->field($model, 'parent')->dropDownList(Category::getCategoryArray($model->tree), ['disabled' => !$model->isNewRecord()]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'sort_field')->dropDownList(['published_at' => 'По дате публикации', 'title' => 'По названию истории', 'episode' => 'По эпизодам'], ['prompt' => 'По умолчанию']) ?>
    <?= $form->field($model, 'sort_order')->dropDownList([SORT_ASC => 'ASC', SORT_DESC => 'DESC'], ['prompt' => 'По умолчанию']) ?>
    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord() ? 'Создать категорию' : 'Сохранить изменения'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<div class="modal remote fade" id="manage-stories-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$remote = Url::to(['category/stories', 'category_id' => $model->getModelID()]);
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
    
    $('#delete-category').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Удалить категорию?')) {
            return;
        }
        $.post($(this).attr('href'));
    });
})();
JS;
$this->registerJs($js);