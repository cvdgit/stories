<?php

declare(strict_types=1);

use common\models\UserStudent;
use modules\edu\models\EduClassProgram;
use yii\web\View;
use yii\widgets\Menu;

/**
 * @var View $this
 * @var array $classProgramItems
 * @var EduClassProgram $classProgram
 * @var UserStudent $student
 * @var string $statWidget
 */

$this->title = 'Статистика';
?>
<div class="container">
    <h1></h1>

    <div style="margin: 30px 0;text-align: center">
        <?= Menu::widget([
            'options' => ['class' => 'class-program-list list-inline'],
            'items' => $classProgramItems,
        ]) ?>
    </div>

    <?= $statWidget; ?>
</div>
