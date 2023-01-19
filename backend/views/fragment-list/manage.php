<?php

declare(strict_types=1);

use yii\bootstrap\Html;
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
#all-items-list .list-group-item {
    cursor: pointer;
}
.list-group-item .list-title {
    cursor: text;
}
.list-item-title {
    cursor: text;
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
                    <li style="display: flex; justify-content: space-between; align-items: center" data-list-id="<?= $listItem['id'] ?>" class="list-group-item fragment-list-item">
                        <div tabindex="-1" class="list-title" contenteditable="true">
                            <?= Html::encode($listItem['name']); ?>
                        </div>
                        <div>
                            <i class="glyphicon glyphicon-trash list-delete"></i>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-title" style="border-bottom: 1px #e5e5e5 solid; margin-bottom: 10px">Слова</div>
            <div>
                <ul id="item-list" class="list-group list-group-flush"></ul>
                <div id="add-word-wrap" style="display: none">
                    <button type="button" class="btn btn-primary btn-sm">Добавить слово</button>
                </div>
            </div>
        </div>
    </div>
</div>
