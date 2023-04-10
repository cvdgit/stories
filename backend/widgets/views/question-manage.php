<?php

declare(strict_types=1);

use yii\bootstrap\Nav;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $items
 * @var string $renderData
 * @var array $createItems
 * @var bool $isCreate
 */
?>
<div class="question-manage" style="height: 100%">
    <div class="row">
        <div class="col-lg-3">
            <div style="background-color: #f8f9fa; box-shadow: inset -1px 0 0 rgb(0 0 0 / 10%); padding: 10px; height: calc(100vh - 150px); display: flex; flex-direction: column">
                <?php if (!$isCreate): ?>
                <div style="margin-bottom: 20px">
                    <div class="btn-group btn-block">
                        <div class="dropdown">
                            <button type="button" data-toggle="dropdown" class="btn btn-block btn-default">Создать вопрос <span class="caret"></span></button>
                            <ul class="dropdown-menu col-lg-12">
                                <?php foreach ($createItems as $item): ?>
                                    <?= '<li>' . Html::a($item['label'], $item['route']) . '</li>' ?>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                <h4>Список вопросов</h4>
                <div style="display: flex; flex-direction: column; overflow: hidden">
                    <div style="display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; flex-grow: 1">
                        <?= Nav::widget([
                            'options' => ['id' => 'nav-question-list', 'class' => 'nav-sidebar nav-sidebar--question', 'style' => 'display: flex; flex-direction: column; height: 100%; overflow-y: auto'],
                            'items' => $items,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <?= $renderData ?>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    const hash = window.location.hash.substring(1);
    if (hash) {
        $("a[data-anchor='" + hash + "']")[0].scrollIntoView({ behavior: "auto", block: "end", inline: "start" });
    }
})();
JS
);
