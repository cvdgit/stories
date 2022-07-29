<?php
use yii\bootstrap\Html;
/**
 * @var string $studentName
 */
?>
<div class="container">
    <div style="padding: 20px 0">
        <div style="display: flex">
            <div style="margin-right: auto">
                <?= Html::a('Родителю', ['/edu/default/switch-to-parent'], ['class' => 'btn btn-small']) ?>
            </div>
            <div>
                <?= $studentName ?>
            </div>
        </div>
    </div>
    <div style="height: 300px">
        Content
    </div>
</div>
