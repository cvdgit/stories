<?php

declare(strict_types=1);

use common\models\UserStudent;
use modules\edu\models\EduClassProgram;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Menu;

/**
 * @var View $this
 * @var UserStudent $student
 * @var EduClassProgram|null $classProgram
 * @var EduClassProgram[] $classPrograms
 * @var string $statWidget
 */

$this->title = 'Статистика';
?>
<div class="container">
    <h1 class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/parent/default/index']) ?> <?= Html::encode($student->name) ?></h1>

    <div style="margin: 30px 0;text-align: center">
        <?= Menu::widget([
            'options' => ['class' => 'class-program-list list-inline'],
            'items' => array_map(static function(EduClassProgram $program) use ($student, $classProgram) {
                return [
                    'label' => $program->program->name,
                    'url' => ['/edu/parent/default/stats', 'id' => $student->id, 'class_program_id' => $program->id],
                    'active' => $classProgram && $classProgram->id === $program->id,
                ];
            }, $classPrograms)
        ]) ?>
    </div>

    <?= $statWidget; ?>
</div>
