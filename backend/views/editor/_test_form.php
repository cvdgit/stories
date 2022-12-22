<?php

declare(strict_types=1);

use backend\models\editor\TestForm;
use backend\widgets\SelectTestWidget;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var TestForm $model
 */
?>
<?= $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput(); ?>
<?php if ($model->haveTest()): ?>
    <div class="form-group">
        <label class="control-label">Тест</label>
        <p class="form-control input-sm"><?= $model->getTestName(); ?></p>
    </div>
<?= $form->field($model, 'test_id')->hiddenInput()->label(false); ?>
<?php else: ?>
<?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->widget(SelectTestWidget::class); ?>
<?php endif; ?>
<div id="test-link" style="<?= empty($model->test_id) ? 'display:none' : ''; ?>">
    <a href="<?= Url::to(['/test/update', 'id' => empty($model->test_id) ? 0 : $model->test_id]); ?>" target="_blank">Перейти к тесту</a>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('#testform-test_id').on('change', function() {
        const testId = $(this).val();
        if (testId) {
            $('#test-link').show();
            $('#test-link a').attr('href',  $('#test-link a').attr('href').replace(/\d+$/, testId));
        } else {
            $('#test-link').hide();
        }
    });
})();
JS
);
