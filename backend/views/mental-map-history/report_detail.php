<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var array $rows
 */

$content = isset($rows[0]) ? $rows[0]['content'] : '';
?>
<div class="mental-map-table" style="padding: 14px 0"><?= $content ?></div>
<div class="table-responsive">
    <table class="table table-bordered mental-map-table">
        <thead>
        <tr>
            <th>User ID</th>
            <th>All</th>
            <th>Hiding</th>
            <th>Target</th>
            <th>Threshold</th>
            <th>Date</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= $row['userName'] ?></td>
                <td><?= $row['overall_similarity'] ?></td>
                <td><?= $row['text_hiding_percentage'] ?></td>
                <td><?= $row['text_target_percentage'] ?></td>
                <td><?= $row['threshold'] ?></td>
                <td><?= SmartDate::dateSmart($row['created_at'], true) ?></td>
                <td>
                    <a href="<?= Url::to(['/mental-map-history/log', 'user_id' => $row['user_id'], 'date' => $row['created_at']]) ?>" class="show-log">Показать</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
