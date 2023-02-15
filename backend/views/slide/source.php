<?php

declare(strict_types=1);

use backend\models\editor\SlideSourceForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var SlideSourceForm $formModel
 */
?>
<?php $form = ActiveForm::begin(['id' => 'slide-source-form']); ?>
<?= $form->field($formModel, 'source')->textarea(['rows' => 10]); ?>
<div class="clearfix">
    <div class="pull-right">
        <button type="submit" class="btn btn-success">Сохранить</button>
        <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
</div>
<?php ActiveForm::end(); ?>
