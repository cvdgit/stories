<?php

declare(strict_types=1);

use backend\Testing\ImportQuestions\Form\QuestionsImportForm;
use backend\widgets\ImportTestWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $testId
 * @var QuestionsImportForm $formModel
 */

$this->registerJs($this->renderFile('@backend/views/questions-import/_questions_import.js'));
?>
<div style="min-height: 50rem">
    <?php $form = ActiveForm::begin(['id' => 'import-questions-form']); ?>
    <div style="display: flex; flex-direction: row; justify-content: stretch; gap: 20px">
        <div style="flex: 2 1 0">
            <div style="display: flex; flex-direction: row; width: 100%">
                <div style="margin-right: 10px">Из теста:</div>
                <div style="flex-grow: 1">
                    <?= $form->field($formModel, 'from_test_id')
                        ->widget(ImportTestWidget::class, [
                            'toTestId' => $testId,
                            'onChange' => 'onTestChange',
                        ])->label(false); ?>
                </div>
            </div>
            <div><p>Вопросы:</p></div>
            <div id="import-questions-list" data-to-test-id="<?= $testId; ?>"></div>
        </div>
        <div style="flex: 1 1 0">
            <div>
                <p>Выбрано вопросов: <strong id="import-questions-count">0</strong></p>
                <button type="submit" id="import-questions" class="btn btn-primary">Импортировать</button>
            </div>
        </div>
    </div>
    <?= Html::activeHiddenInput($formModel, 'to_test_id'); ?>
    <?php ActiveForm::end(); ?>
</div>
