<?php

declare(strict_types=1);

use backend\Testing\Questions\ImageGaps\Update\UpdateImageGapsForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var UpdateImageGapsForm $formModel
 */

$this->registerCss(<<<CSS
.image-container-wrapper {
    overflow: hidden;
    margin-top: 10px;
    border: 1px #d0d0d0 solid;
}
.image-container-wrapper svg {
    outline: 0;
}
.image-gaps {

}
.image-gaps .scheme-mark {
    fill-opacity: .3 !important;
    fill: #a94442 !important;
    stroke: #2a2a2a !important;
}
CSS
);
?>
<?php $form = ActiveForm::begin(['id' => 'image-gaps-form']); ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($formModel, 'max_prev_items')->dropDownList($formModel->getMaxPrevItems())
    ->hint('При неправильном выборе возврат на указанное количество элементов'); ?>
<div style="margin-bottom: 20px">
    <div class="row">
        <div class="col-md-5">
            <div class="btn-group" id="select-shapes" data-toggle="buttons">
                <label class="btn btn-default active">
                    <input type="radio" name="shape" value="move" autocomplete="off" checked>
                    <i class="glyphicon glyphicon-move"></i>
                </label>
                <label class="btn btn-default">
                    <input type="radio" name="shape" value="rect" autocomplete="off"> Выделить
                </label>
            </div>
        </div>
        <div class="col-md-7"></div>
    </div>
    <div class="image-container-wrapper">
        <div id="image-container" style="max-height: 500px" class="image-gaps"></div>
    </div>
</div>
<div>
    <?= Html::activeHiddenInput($formModel, 'payload'); ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
</div>
<?php ActiveForm::end(); ?>
