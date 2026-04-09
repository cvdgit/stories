<?php

declare(strict_types=1);

use backend\widgets\SelectStoryWidget;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use modules\edu\widgets\SelectItemsWidget;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var $formModel
 * @var $userModel
 * @var $storyModel
 */
?>
<div style="display: flex; flex-direction: row; gap: 20px">
    <div style="flex: 1; display: flex; flex-direction: column; gap: 10px">
        <div style="display: flex; flex-direction: row; justify-content: space-between">
            <div class="studentIdValue" style="flex: 1">
                <?= $form->field($formModel, 'studentId')->widget(SelectItemsWidget::class, [
                    'loadUrl' => ['/edu/teacher/required-story/select-students'],
                    'onChange' => 'requiredSelectStudent',
                    'userModel' => $userModel,
                ]) ?>
            </div>
        </div>
        <div style="display: flex; flex-direction: row; justify-content: space-between">
            <div class="storyIdValue" style="flex: 1">
                <?= $form->field($formModel, 'storyId')->widget(SelectStoryWidget::class, [
                    'loadUrl' => ['/edu/teacher/required-story/select-stories'],
                    'onChange' => 'requiredSelectStory',
                    'storyModel' => $storyModel,
                ]) ?>
            </div>
        </div>
        <?= $form->field($formModel, 'startDate')->textInput(['type' => 'date']) ?>
        <?= $form->field($formModel, 'days')->textInput(['type' => 'number', 'class' => 'form-control required-story-days'])->hint('Плановое количество дней на прохождение истории') ?>
        <?= $form->field($formModel, 'status')->dropDownList(RequiredStoryStatus::values(), ['prompt' => 'Выберите состояние']) ?>
    </div>
    <div style="flex: 1">
        <div id="studentFactAnswersWrap" style="margin-bottom: 20px; display: <?= empty($formModel->storyStudentFact) ? 'none' : 'block' ?>">
            <div style="margin-bottom: 10px">Пройдено учеником на текущий момент: <b id="studentFactAnswers"><?= $formModel->storyStudentFact ?></b></div>
            <div style="display: flex; flex-direction: row; align-items: center; gap: 10px">
                <label style="margin: 0" for="useStudentFactAnswers">Учитывать пройденные:</label>
                <input id="useStudentFactAnswers" style="margin: 0" type="checkbox">
            </div>
        </div>
        <div id="metadata-container"></div>
        <?= $form->field($formModel, 'metadata')->hiddenInput(['class' => 'required-story-metadata'])->label(false)->error(false) ?>
        <?= $form->field($formModel, 'studentId')->hiddenInput(['class' => 'required-story-student-id'])->label(false)->error(false) ?>
    </div>
</div>
