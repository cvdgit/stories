<?php

declare(strict_types=1);

use yii\helpers\Html;

/**
 * @var string $studentName
 */
?>
<div style="padding: 20px 0; margin-bottom: 20px">
    <div style="display: flex">
        <div style="margin-right: auto">
            <?= Html::a('Родителю', ['/edu/default/switch-to-parent'], ['class' => 'btn btn-small']) ?>
        </div>
        <div>
            <?= $studentName ?>
        </div>
    </div>
</div>
