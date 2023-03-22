<?php

declare(strict_types=1);

/**
 * @var array $rows
 * @var int $studentId
 */

use yii\bootstrap\Html;
use yii\helpers\Url;

?>
<div>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Тест</th>
                <th>Расписание</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr data-test-id="<?= $row['testId']; ?>">
                <td><?= Html::encode($row['testName']); ?></td>
                <td><?= Html::encode($row['scheduleName']); ?></td>
                <td class="status"></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div>
    <a id="create-next-repetition" class="btn btn-primary" href="<?= Url::to(['/repetition/student/next-repetition', 'student_id' => $studentId]); ?>">Создать следующие повторения</a>
</div>
