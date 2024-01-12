<?php

declare(strict_types=1);

use backend\Story\Tests\UpdatePassTestsRepeat\UpdatePassTestsRepeatFormAction;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var array $rows
 * @var int $storyId
 * @var UpdatePassTestsRepeatFormAction $formModel
 */
?>
<div>
    <?php $form = ActiveForm::begin(["action" => ["/story-test/update-pass-test-repeat"], "id" => "update-pass-test-repeat-form"]); ?>
    <div>
        <?= $form->field($formModel, "repeat")->dropDownList(["Начало", "1 элемент", "2 элемента", "3 элемента", "4 элемента", "5 элементов"], ["prompt" => "Выберите значение"]); ?>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Тест</th>
            <th>Вопрос</th>
            <th>Возврат на</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($rows as $row): ?>
            <tr data-question-id="<?= $row['questionId']; ?>">
                <td><?= Html::encode($row['testName']); ?></td>
                <td><?= Html::encode($row['questionName']); ?></td>
                <td><?= Html::encode($row["questionPrevItems"]); ?></td>
                <td class="status"></td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
    <?php ActiveForm::end(); ?>
</div>
<div>
    <button type="submit" form="update-pass-test-repeat-form" class="btn btn-primary">Изменить</button>
</div>
