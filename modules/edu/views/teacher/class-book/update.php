<?php

declare(strict_types=1);

use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var ClassBookForm $formModel
 */

$this->title = $formModel->name;
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1><?= Html::encode($this->title) ?></h1>

</div>
