<?php

declare(strict_types=1);

use yii\widgets\ListView;

?>
<div class="container">
    <h1 style="margin-top: 0; margin-bottom: 20px">Повторения</h1>
    <div style="margin-bottom: 40px">
        <?= ListView::widget([
            'dataProvider' => $repetitionDataProvider,
            'summary' => false,
            'itemView' => '_repetition_item',
            'itemOptions' => ['tag' => false],
            'layout' => "{summary}\n<div class=\"row\">{items}</div>",
        ]) ?>
    </div>
</div>
