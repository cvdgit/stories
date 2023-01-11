<?php

declare(strict_types=1);

use backend\forms\FragmentListForm;
use backend\forms\FragmentListItemForm;
use dosamigos\selectize\SelectizeTextInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var FragmentListForm $formModel
 * @var FragmentListItemForm $itemFormModel
 */

$this->registerCss(<<<CSS
.list-item {
    display: flex;
    flex-direction: row;
}
.list-item .form-group {
    margin-bottom: 0;
    margin-right: auto;
    width: 100%;
    padding-right: 20px;
}
.line-number {
    min-width: 30px;
}
.item-actions {
    min-width: 100px;
}
CSS
);
?>

<?php $form = ActiveForm::begin([
    'options' => [
        'id' => 'create-list-form'
    ],
]); ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true]); ?>

<div>
    <h3 class="h4">Слова</h3>
    <div id="list">
        <div class="list-item">
            <div class="line-number">1</div>
            <?= $form->field($itemFormModel, '[0]name')->textInput(['maxlength' => true])->label(false); ?>
            <div class="item-actions"></div>
        </div>
    </div>
    <div style="padding: 20px 0">
        <a href="" id="add-control">Добавить слово</a>
    </div>
    <?= $form->field($formModel, 'keywords')->widget(SelectizeTextInput::class, [
        'loadUrl' => ['tag/list'],
        'options' => ['class' => 'form-control'],
        'clientOptions' => [
            'plugins' => ['remove_button'],
            'valueField' => 'name',
            'labelField' => 'name',
            'searchField' => ['name'],
            'create' => true,
        ],
    ]); ?>
</div>

<?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs(<<<JS
(function() {

    const resetItemLineNumbers = (items) => {
        items.each(function(i) {
            $(this).find('.line-number').text(++i);
        });
    }

    let itemIndex = 1;
    $('#add-control').on('click', function(e) {
        e.preventDefault();

        const newItem = $('#list .list-item:eq(0)').clone();
        newItem.find('.item-actions')
            .append(
                $('<a/>', {href: ''})
                    .text('Удалить')
                    .on('click', function(ev) {
                        ev.preventDefault();
                        $(this).parents('.list-item:eq(0)').remove();
                        resetItemLineNumbers($('#list .list-item'));
                    })
            );

        newItem.find('input').val('').focus();
        newItem.find('input').attr('id', newItem.find('input').attr('id').replace('0', itemIndex));
        newItem.find('input').attr('name', newItem.find('input').attr('name').replace('[0]', '[' + itemIndex + ']'));

        $('#list').append(newItem);
        itemIndex++;

        resetItemLineNumbers($('#list .list-item'));
    });
})();
JS
);
