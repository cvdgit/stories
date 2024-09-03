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
 */

$this->title = 'История прохождения ментальных карт';

$this->registerCss(<<<CSS
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
CSS
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
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Ментальная карта</th>
                                <th>Фрагмент</th>
                                <th>% сходства</th>
                                <th>% закрытия текста</th>
                                <th>Текст при ответе</th>
                                <th>Дата</th>
                            </tr>
                            </thead>
                            <?php
                            $data = array_filter(
                                $historyByUser[$user['id']],
                                static function (array $row) use ($mentalMap): bool {
                                    return $row['mental_map_id'] === $mentalMap->uuid;
                                },
                            );
                            ?>
                            <tbody>
                            <?php
                            if (count($data) === 0): ?>
                                <tr>
                                    <td colspan="6">Нет данных</td>
                                </tr>
                            <?php
                            else: ?>
                                <?php
                                foreach ($data as $history): ?>
                                    <tr>
                                        <td><?= $mentalMap->name . ' (Слайд ' . $history['slideNumber'] . ')'; ?></td>
                                        <td>
                                            <?php
                                            $imageFragment = $mentalMap->findImageFromPayload(
                                                $history['image_fragment_id'],
                                            );
                                            echo $imageFragment['text']; ?>
                                        </td>
                                        <td><?= $history['overall_similarity']; ?></td>
                                        <td><?= $history['text_hiding_percentage']; ?></td>
                                        <td>
                                            <div class="detail-text">
                                                <?= $history['content']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= SmartDate::dateSmart(
                                                $history['created_at'],
                                                true,
                                            ); ?>
                                        </td>
                                    </tr>
                                <?php
                                endforeach; ?>
                            <?php
                            endif; ?>
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
