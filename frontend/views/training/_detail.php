<?php

declare(strict_types=1);

use common\helpers\SmartDate;
use frontend\MentalMap\MentalMap;
use yii\web\View;

/**
 * @var View $this
 * @var array $data
 * @var array<MentalMap> $mentalMaps
 */

$this->registerCss(
    <<<CSS
.target-text {
    font-weight: bold;
}
.text-item-word {
    display: inline-block;
    white-space: nowrap;
    margin-right: 8px;
    user-select: none;
    cursor: pointer;
    margin-bottom: 6px;
}
.word-target {
    font-weight: 600;
}
.text-item-word.selected, .user-response .target-text {
    color: #fff;
    border: 1px #808080 solid;
    cursor: pointer;
}
.data-col {
  font-size: 14px;
}
CSS,
);
?>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 10%">Дата</th>
            <th style="width: 30%">Фрагмент</th>
            <th style="width: 30%">Текст при ответе</th>
            <th>Порог</th>
            <th style="width: 10%">% сходства</th>
            <th style="width: 10%">% закрытия текста</th>
            <th style="width: 10%">% закрытия важного текста</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $imageData): ?>
        <?php $fragment = $mentalMaps[$imageData['mental_map_id']]->findImageFromPayload($imageData['image_fragment_id']); ?>
            <tr>
                <td class="data-col"><?= SmartDate::dateSmart($imageData['created_at'], true) ?></td>
                <td class="data-col"><?= $fragment === null ? '-' : $fragment['text'] ?? $fragment['title'] ?></td>
                <td class="data-col user-response"><?= $imageData['content'] ?></td>
                <td class="data-col"><?= $imageData['threshold'] ?></td>
                <td class="data-col <?= MentalMap::fragmentIsDone((int) $imageData['overall_similarity'], (int) $imageData['threshold']) ? 'bg-success' : 'bg-danger' ?>"><?= $imageData['overall_similarity'] ?></td>
                <td class="data-col"><?= $imageData['text_hiding_percentage'] ?></td>
                <td class="data-col"><?= $imageData['text_target_percentage'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
