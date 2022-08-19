<?php

declare(strict_types=1);

use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'Задания';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="min-height: 500px">
        Задания
    </div>
</div>
