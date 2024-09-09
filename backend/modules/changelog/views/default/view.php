<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var Changelog $changelog
 */

use backend\modules\changelog\models\Changelog;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\HtmlPurifier;

?>
<div class="modal-header" style="display: flex; justify-content: space-between">
    <h5 class="modal-title" style="margin-right: auto"><?= Html::encode($changelog->title) ?></h5>
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
</div>
<div class="modal-body">
    <div class="changelog-wrap">
        <?= HtmlPurifier::process($changelog->text) ?>
    </div>
</div>
