<?php

declare(strict_types=1);

use common\models\UserStudent;
use modules\edu\models\EduClass;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var UserStudent $student
 * @var EduClass $studentClass
 * @var DataProviderInterface $dataProvider
 * @var View $this
 * @var DataProviderInterface $repetitionDataProvider
 * @var string $studentToolbarWidget
 */

$this->title = $student->name;
?>
<div class="container">
    <?= $studentToolbarWidget; ?>

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
                'layout' => "{summary}\n<div class=\"row\">{items}</div>",
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
            'viewParams' => ['studentId' => $student->id],
            'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
        ]) ?>
    </div>
</div>
