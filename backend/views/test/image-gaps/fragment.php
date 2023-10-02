<?php

declare(strict_types=1);

use backend\Testing\Questions\ImageGaps\Fragment\FragmentItemForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var FragmentItemForm $itemFormModel
 */

$this->registerCss(<<<CSS
.list-item {
    display: flex;
}
CSS
);
?>

<div>
    <table id="list" class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Ответ</th>
                <th>Правильный</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div>
        <?php $form = ActiveForm::begin([
            'id' => 'fragment-form',
            'options' => ['class' => 'form-inline'],
        ]); ?>
        <?= $form->field($itemFormModel, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label(false); ?>
        <?= $form->field($itemFormModel, 'correct')->checkbox(); ?>
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
