<?php
use common\helpers\Url;
use yii\bootstrap\Nav;
/** @var array $items */
/** @var string $renderData */
/** @var array $createRoute */
?>
<div class="question-manage" style="height: 100%">
    <div class="row">
        <div class="col-lg-3">
            <div style="background-color: #f8f9fa; box-shadow: inset -1px 0 0 rgb(0 0 0 / 10%); padding: 10px; height: 100%">
                <div style="margin-bottom: 20px">
                    <a href="<?= Url::to($createRoute) ?>" class="btn btn-primary btn-block">Создать ответ</a>
                </div>
                <h4>Список ответов</h4>
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
