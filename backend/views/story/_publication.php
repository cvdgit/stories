<?php

use yii\helpers\Html;

/** @var $published bool */
/** @var $text string */
/** @var $action [] */
?>
<div class="alert alert-<?= $published ? 'success' : 'warning' ?>">
    <div class="clearfix">
        <div class="pull-left" style="line-height: 34px"><?= $text ?></div>
        <div class="pull-right">
            <?= Html::beginForm($action) . Html::submitButton($published ? 'Снять с публикации' : 'Опубликовать', ['class' => 'btn btn-primary']) . Html::endForm() ?>
        </div>
    </div>
</div>
