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
.text-item-word.selected {
    color: #fff;
    border: 1px #808080 solid;
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
                <td><?= SmartDate::dateSmart($imageData['created_at'], true) ?></td>
                <td><?= $fragment === null ? '-' : $fragment['text'] ?></td>
                <td><?= $imageData['content'] ?></td>
                <td><?= $imageData['threshold'] ?></td>
                <td><?= $imageData['overall_similarity'] ?></td>
                <td><?= $imageData['text_hiding_percentage'] ?></td>
                <td><?= $imageData['text_target_percentage'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
