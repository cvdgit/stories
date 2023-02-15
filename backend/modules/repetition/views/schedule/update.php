<?php

declare(strict_types=1);

use backend\modules\repetition\Schedule\ScheduleForm;
use backend\modules\repetition\Schedule\ScheduleItemForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var View $this
 * @var ScheduleForm $formModel
 * @var ScheduleItemForm $itemFormModel
 */

$this->title = 'Изменение расписания';

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
<h1 class="h2 page-header">
    <a href="<?= Url::to(['/repetition/schedule/index']); ?>"><i class="glyphicon glyphicon-arrow-left back-arrow"></i></a>
    <?= Html::encode($this->title) ?>
</h1>

<div class="row">
    <div class="col-lg-8">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($formModel, 'name')->textInput(['maxlength' => true]); ?>
        <h3 class="h4">Часы</h3>
        <div id="list">
            <?php foreach ($formModel->getItems() as $i => $item): ?>
            <div class="list-item">
                <?= $form->field($item, "[$i]hours")->textInput(['maxlength' => true])->label(false); ?>
                <div class="item-actions">
                    <a href="#" class="delete-item">Удалить</a>
                </div>
                <?= Html::activeHiddenInput($item, "[$i]id"); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="padding-bottom: 20px">
            <a href="#" id="add-control">Добавить</a>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {

    const resetItemLineNumbers = (items) => {
        items.each(function(i) {
            $(this).find('.line-number').text(++i);
        });
    }

    let itemIndex = $('#list').find('.list-item').length + 1;
    $('#add-control').on('click', function(e) {
        e.preventDefault();

        const newItem = $('#list .list-item:eq(0)').clone();

        newItem.find('input').each((i, elem) => {
            const el = $(elem);
            el
                .val('')
                .attr('id', el.attr('id').replace('0', itemIndex))
                .attr('name', el.attr('name').replace('[0]', '[' + itemIndex + ']'));
        });

        $('#list').append(newItem);
        itemIndex++;

        resetItemLineNumbers($('#list .list-item'));
    });

    $('#list').on('click', '.delete-item', function(e) {
        e.preventDefault();
        $(this).parents('.list-item:eq(0)').remove();
        resetItemLineNumbers($('#list .list-item'));
    });
})();
JS
);
