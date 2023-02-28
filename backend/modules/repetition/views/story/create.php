<?php

declare(strict_types=1);

use backend\modules\repetition\Repetition\StoryCreate\CreateStoryRepetitionForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var CreateStoryRepetitionForm $formModel
 * @var array $studentItems
 * @var array $scheduleItems
 * @var array{int, array{testId: int, testName: string}} $storyTests
 */
?>
<?php $form = ActiveForm::begin(['id' => 'create-repetition-form']); ?>
<?= $form->field($formModel, 'student_id')->dropDownList($studentItems, ['prompt' => 'Выберите ученика']); ?>
<?= $form->field($formModel, 'schedule_id')->dropDownList($scheduleItems, ['prompt' => 'Выберите расписание']); ?>
<div id="test-items-list">
    <?php foreach ($storyTests as $testItem): ?>
    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; height: 40px; cursor: pointer">
        <div><?= Html::encode($testItem['testName']); ?></div>
        <div class="test-actions" style="width: 100px; text-align: center">
            <div class="checkbox">
                <label>
                    <input name="test_id" type="checkbox" value="<?= $testItem['testId'] ?>" checked>
                </label>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="form-group">
    <button type="submit" class="btn btn-success">Создать</button>
</div>
<?php ActiveForm::end(); ?>
