<?php

declare(strict_types=1);

use backend\forms\FragmentListSearch;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var array $items
 * @var FragmentListSearch $searchFormModel
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

<div>
    <?php $form = ActiveForm::begin(['id' => 'lists-filter-form', 'options' => ['class' => 'form-inline']]); ?>
    <?= $form->field($searchFormModel, 'my_lists')->checkbox(); ?>
    <?= $form->field($searchFormModel, 'for_current_test')->checkbox(); ?>
    <?php ActiveForm::end(); ?>
</div>

<?php Pjax::begin(['id' => 'pjax-lists', 'enablePushState' => false]); ?>

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
                            <div class="items">
                                <?php foreach ($listItem['items'] as $item): ?>
                                <span data-item-id="<?= $item['id']; ?>" class="label label-success list-item"><?= $item['name']; ?></span>
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
            <div>
                <button id="create-fragment-list" type="button" class="btn btn-primary">Вставить список</button>
            </div>
        </div>
    </div>
</div>

<?php Pjax::end(); ?>
