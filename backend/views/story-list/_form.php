<?php
use backend\widgets\SelectCategoriesWidget;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\story_list\CreateStoryListForm|backend\models\story_list\UpdateStoryListForm */
$isNewRecord = \is_a($model, backend\models\story_list\CreateStoryListForm::class);
$className = substr(strrchr(get_class($model), '\\'), 1);
?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<div id="selected-categories" style="margin:10px 0">
    <?php foreach ($model->categories as $i => $category): ?>
    <?= Html::hiddenInput($className . "[categories][$i]", $category->id) ?>
    <span class="label label-default"><?= Html::encode($category->name) ?></span>
    <?php endforeach ?>
</div>
<?= SelectCategoriesWidget::widget([
    'selectInputID' => '#selected-categories',
    'treeID' => 1,
    'onSelect' => 'selectCategories',
]) ?>
<div class="form-group">
    <?= Html::submitButton($isNewRecord ? 'Создать список' : 'Сохранить изменения', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs(<<<JS
function selectCategories(items) {
    var modelName = '$className';
    var list = $('#selected-categories');
    list.empty();
    items.forEach(function(item, i) {
        $('<input/>', {
            'type': 'hidden',
            'name': modelName + '[categories][' + i + ']',
            'value': item.id
        }).appendTo(list);
        $('<span/>', {
            'class': 'label label-default',
            'text': item.name
        }).appendTo(list);
        list.append(' ');
    });
}
JS
);