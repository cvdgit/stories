<?php

declare(strict_types=1);

use backend\models\editor\QuestionForm;
use backend\widgets\SelectTestWidget;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var QuestionForm $model
 */
?>
<?php if ($model->haveTest()): ?>
    <div class="form-group">
        <label class="control-label">Тест</label>
        <p class="form-control input-sm"><?= $model->getTestName(); ?></p>
    </div>
    <?= $form->field($model, 'test_id')->hiddenInput()->label(false); ?>
<?php else: ?>
    <?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->widget(SelectTestWidget::class); ?>
<?php endif; ?>
<?= $form->field($model, 'required', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
