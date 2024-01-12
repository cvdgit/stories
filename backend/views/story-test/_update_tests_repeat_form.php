<?php

declare(strict_types=1);

use backend\Story\Tests\UpdateTestsRepeat\UpdateTestsRepeatForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var array $rows
 * @var int $storyId
 * @var UpdateTestsRepeatForm $formModel
 */
?>
<div>
    <?php $form = ActiveForm::begin(["action" => ["/story-test/update-repeat"], "id" => "update-repeat-form"]); ?>
    <div>
        <?= $form->field($formModel, "repeat")->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5], ["prompt" => "Выберите значение"]); ?>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Тест</th>
            <th>Повторов</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($rows as $row): ?>
            <tr data-test-id="<?= $row['testId']; ?>">
                <td><?= Html::encode($row['testName']); ?></td>
                <td><?= Html::encode($row['testRepeat']); ?></td>
                <td class="status"></td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
    <?php ActiveForm::end(); ?>
</div>
<div>
    <button type="submit" form="update-repeat-form" class="btn btn-primary">Изменить</button>
</div>
