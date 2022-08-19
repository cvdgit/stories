<?php

declare(strict_types=1);

use modules\edu\models\EduProgram;
use yii\widgets\Menu;

/**
 * @var EduProgram[] $programs
 */

$items = array_map(static function(EduProgram $program) {
    return ['label' => $program->name, 'url' => ['/edu/student/topic', 'id' => $program->id]];
}, $programs);
?>
<?= Menu::widget([
    'options' => ['class' => 'story-category-list'],
    'items' => $items,
]) ?>
