<?php

declare(strict_types=1);

use modules\edu\widgets\AdminToolbarWidget;
use yii\web\View;

/**
 * @var View $this
 */

$this->title = 'Управление обучением';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>
</div>
