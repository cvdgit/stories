<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\web\View;

/**
 * @var View $this
 * @var array $rows
 */

$this->title = 'Прогресс прохождения истории в обучении';
?>
<?= AdminToolbarWidget::widget() ?>

<?= AdminHeaderWidget::widget([
    'title' => $this->title,
    'content' => '',
]) ?>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= $row['studentName'] ?></td>
            <td><?= $row['sessionId'] ?></td>
            <td><?= SmartDate::dateSmart($row['time'], true) ?></td>
            <td><?= $row['slideNumber'] ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
