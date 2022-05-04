<?php
use yii\bootstrap\Html;
/** @var string $errorText */
?>
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
    <?= Html::encode($errorText) ?>
</div>
