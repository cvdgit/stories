<?php
use yii\bootstrap\Nav;
use yii\bootstrap\Html;
/** @var array $items */
/** @var string $renderData */
/** @var array $createItems */
/** @var bool $isCreate */
?>
<div class="question-manage" style="height: 100%">
    <div class="row">
        <div class="col-lg-3">
            <div style="background-color: #f8f9fa; box-shadow: inset -1px 0 0 rgb(0 0 0 / 10%); padding: 10px; height: 100%">
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
                <?= Nav::widget([
                    'options' => ['class' => 'nav-sidebar nav-sidebar--question'],
                    'items' => $items,
                ]) ?>
            </div>
        </div>
        <div class="col-lg-9">
            <?= $renderData ?>
        </div>
    </div>
</div>
