<?php

declare(strict_types=1);

use backend\MentalMap\FetchMentalMapUserHistory\MentalMapUserHistoryItem;
use backend\MentalMap\MentalMap;
use common\helpers\SmartDate;
use yii\web\View;

/**
 * @var View $this
 * @var MentalMap[] $mentalMaps
 * @var MentalMapUserHistoryItem[] $historyData
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4 class="modal-title">Ментальные карты</h4>
</div>
<div class="modal-body">
    <div>
        <?php
        foreach ($mentalMaps as $mentalMap): ?>
            <h3 class="h4"><?= $mentalMap->name; ?></h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width: 30%">Фрагмент</th>
                        <th style="width: 30%">Текст при ответе</th>
                        <th style="width: 10%">% сходства</th>
                        <th style="width: 10%">% закрытия текста</th>
                        <th style="width: 10%">% закрытия важного текста</th>
                        <th style="width: 10%">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($mentalMap->getImages() as $image): ?>
                        <?php
                        /** @var MentalMapUserHistoryItem|null $imageData */
                        $imageData = array_values(
                            array_filter(
                                $historyData,
                                static function (MentalMapUserHistoryItem $item) use ($mentalMap, $image): bool {
                                    return $item->getMentalMapId() === $mentalMap->uuid && $item->getImageFragmentId(
                                        ) === $image['id'];
                                },
                            ),
                        )[0] ?? null;
                        if ($imageData === null) {
                            continue;
                        }
                        ?>
                        <tr>
                            <td><?= $image['text']; ?></td>
                            <td><?= $imageData->getContent() ?? '-'; ?></td>
                            <td><?= $imageData->getAll() ?? '-'; ?></td>
                            <td><?= $imageData->getHiding() ?? '-'; ?></td>
                            <td><?= $imageData->getTarget() ?? '-' ?></td>
                            <td><?= SmartDate::dateSmart($imageData->getCreatedAt(), true) ?></td>
                        </tr>
                    <?php
                    endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php
        endforeach ?>
    </div>
</div>
