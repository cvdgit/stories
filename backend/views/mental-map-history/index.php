<?php

declare(strict_types=1);

use backend\MentalMap\MentalMap;
use common\helpers\SmartDate;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var MentalMap[] $mentalMaps
 * @var array $historyByUser
 * @var array $users
 * @var array $sidebarMenuItems
 * @var array $breadcrumbs
 */

$this->title = 'История прохождения ментальных карт';
$this->params = array_merge($this->params, $sidebarMenuItems);
$this->params = array_merge($this->params, $breadcrumbs);

$this->registerCss(
    <<<CSS
.detail-text {

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
.text-item-word.selected {
    color: #fff;
    border: 1px #808080 solid;
}
CSS,
);
?>
<div>
    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2"
            class="h2"><?= $this->title ?></h1>
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <?php
        foreach ($users as $i => $user): ?>
            <li role="presentation" class="<?= $i === 0 ? 'active' : ''; ?>">
                <a href="#user<?= $user['id']; ?>" aria-controls="home" role="tab" data-toggle="tab"><?= Html::encode(
                        $user['name'],
                    ); ?></a>
            </li>
        <?php
        endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php
        foreach ($users as $i => $user): ?>
            <div role="tabpanel" class="tab-pane<?= $i === 0 ? ' active' : ''; ?>" id="user<?= $user['id']; ?>"
                 style="padding: 20px 0">
                <?php
                foreach ($mentalMaps as $mentalMap): ?>
                    <h3 class="h4"><?= $mentalMap->name; ?></h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 35%">Фрагмент</th>
                                <th style="width: 35%">Текст при ответе</th>
                                <th style="width: 10%">% сходства</th>
                                <th style="width: 10%">% закрытия текста</th>
                                <th style="width: 10%">Дата</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($mentalMap->getImages() as $image): ?>
                                <?php
                                $imageData = array_values(
                                    array_filter(
                                        $historyByUser[$user['id']],
                                        static function (array $row) use ($mentalMap, $image): bool {
                                            return $row['mentalMapId'] === $mentalMap->uuid && $row['imageFragmentId'] === $image['id'];
                                        },
                                    ),
                                )[0] ?? [];
                                ?>
                                <tr>
                                    <td><?= $image['text']; ?></td>
                                    <td><?= $imageData['content'] ?? '-'; ?></td>
                                    <td><?= $imageData['all'] ?? '-'; ?></td>
                                    <td><?= $imageData['hiding'] ?? '-'; ?></td>
                                    <td><?= isset($imageData['createdAt']) ? SmartDate::dateSmart(
                                            $imageData['createdAt'],
                                            true,
                                        ) : '-'; ?></td>
                                </tr>
                            <?php
                            endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                endforeach; ?>
            </div>
        <?php
        endforeach; ?>
    </div>
</div>
