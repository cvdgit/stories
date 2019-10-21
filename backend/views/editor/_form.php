<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'id' => 'block-form',
]);
/** @var $model backend\models\editor\BaseForm */
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'width') ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'top', ['inputOptions' => ['class' => 'form-control editor-top']]) ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'height') ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'left', ['inputOptions' => ['class' => 'form-control editor-left']]) ?></div>
</div>
<?php

/** @var $this yii\web\View  */
echo $this->render($model->view, ['form' => $form, 'model' => $model]);

echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo $form->field($model, 'block_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
echo Html::a('Удалить блок', '#', ['class' => 'btn btn-danger', 'onclick' => "StoryEditor.deleteBlock('" . $model->block_id . "')"]);
ActiveForm::end();
