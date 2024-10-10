<?php

declare(strict_types=1);

use backend\models\editor\MentalMapForm;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var MentalMapForm $model
 */
?>
<div class="form-group">
    <label class="control-label">Ментальная карта</label>
    <p class="form-control input-sm"><?= $model->mental_map_id ?></p>
</div>
<?= $form->field($model, 'mental_map_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'required', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
