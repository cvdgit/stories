<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var array $items
 */

$this->registerCss(<<<CSS
.dual-list {
  min-height: 300px;
}
.dual-list .item-add, .dual-list .item-del {
  cursor: pointer;
}
.col-title {
  padding: 0.75rem 0;
  font-weight: bold;
}
.selected-item {
    display: flex;
    justify-content: space-between;
}
.selected-item p {
    margin: 0;
}
.add-items, .selected-del {
    cursor: pointer;
}
CSS
);
?>
<div class="dual-list">
    <div class="row">
        <div class="col-md-6">
            <div class="col-title" style="border-bottom: 1px #e5e5e5 solid; margin-bottom: 10px">Списки</div>
            <div>
                <ul id="all-items-list" class="list-group list-group-flush">
                    <?php foreach ($items as $listItem): ?>
                    <li style="display: flex; justify-content: space-between; align-items: center" data-list-id="<?= $listItem['id'] ?>" class="list-group-item">
                        <div>
                            <h4><?= $listItem['name']; ?></h4>
                            <div class="items hide">
                                <?php foreach ($listItem['items'] as $item): ?>
                                <div data-item-id="<?= $item['id']; ?>" class="list-item"><?= $item['name']; ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <i class="glyphicon glyphicon-plus add-items"></i>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-title" style="border-bottom: 1px #e5e5e5 solid; margin-bottom: 10px">Итоговый список</div>
            <div>
                <ul id="selected-items-list" class="list-group list-group-flush"></ul>
            </div>
        </div>
    </div>
</div>
<div>
    <button id="create-fragment-list" type="button" class="btn btn-primary">Вставить список</button>
</div>
