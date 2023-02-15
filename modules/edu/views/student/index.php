<?php

declare(strict_types=1);

use common\models\UserStudent;
use modules\edu\widgets\StudentToolbarWidget;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var UserStudent $student
 * @var DataProviderInterface $dataProvider
 * @var View $this
 * @var DataProviderInterface $repetitionDataProvider
 */

$this->title = $student->name;
?>
<div class="container">
    <?= StudentToolbarWidget::widget(['student' => $student]) ?>

    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2">Повторение</h1>
    </div>

    <div>
        <div style="margin-bottom: 40px">
            <?= ListView::widget([
                'dataProvider' => $repetitionDataProvider,
                'summary' => false,
                'itemView' => '_repetition_item',
                'itemOptions' => ['tag' => false],
                //'viewParams' => ['classId' => $student->class_id, 'studentId' => $student->id],
                'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
            ]) ?>
        </div>
    </div>

    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2">Обучение</h1>
    </div>

    <div style="margin-bottom: 40px">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'itemView' => '_program_item',
            'itemOptions' => ['tag' => false],
            'viewParams' => ['classId' => $student->class_id, 'studentId' => $student->id],
            'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
        ]) ?>
    </div>
</div>
