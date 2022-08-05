<?php

use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\widgets\ListView;

/**
 * @var string $studentName
 * @var DataProviderInterface $dataProvider
 * @var int $classId
 */

$this->title = $studentName;
?>
<div class="container">

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

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'itemView' => '_program_item',
        'itemOptions' => ['tag' => false],
        'viewParams' => ['classId' => $classId],
        'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
    ]) ?>
</div>
