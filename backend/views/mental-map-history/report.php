<?php

declare(strict_types=1);

use yii\web\View;
use backend\MentalMap\MentalMap;

/**
 * @var View $this
 * @var string $storyName
 * @var array<array-key, MentalMap> $mentalMaps
 */

$this->title = 'Сводный отчет - ' . $storyName;

$this->registerJs($this->renderFile('@backend/views/mental-map-history/report.js'));
?>
<div>
    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2"
            class="h2"><?= $this->title ?></h1>
    </div>
    <div>
        <?php
        foreach ($mentalMaps as $mentalMap): ?>
            <h3 class="h4"><?= $mentalMap->name ?></h3>
            <div class="table-responsive">
                <table class="table table-bordered mental-map-table" data-map-id="<?= $mentalMap->uuid ?>">
                    <thead>
                    <tr>
                        <th style="width: 30%">Фрагмент</th>
                        <th>Всего попыток</th>
                        <th>Неправильных</th>
                        <th>Пользователи</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($mentalMap->getItems() as $image): ?>
                        <tr data-fragment-id="<?= $image['id'] ?>">
                            <td><?= $image['text'] ?? $image['title'] ?></td>
                            <td class="fragment-count"></td>
                            <td class="fragment-correct"></td>
                            <td class="fragment-users"></td>
                        </tr>
                    <?php
                    endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php
        endforeach; ?>
    </div>
</div>
