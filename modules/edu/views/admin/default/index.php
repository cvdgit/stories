<?php
use yii\bootstrap\Html;
?>
<div>
    <ul>
        <li><?= Html::a('Классы', ['/edu/admin/class/index']) ?></li>
        <li><?= Html::a('Предметы', ['/edu/admin/program/index']) ?></li>
        <li><?= Html::a('Программы обучения', ['/edu/admin/class-program/index']) ?></li>
        <li><?= Html::a('Темы', ['/edu/admin/topic/index']) ?></li>
    </ul>
</div>
