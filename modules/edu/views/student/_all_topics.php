<?php

declare(strict_types=1);

use modules\edu\models\EduTopic;
use yii\widgets\Menu;

/**
 * @var EduTopic[] $topics
 */

$items = array_map(static function(EduTopic $topic) {
    return ['label' => $topic->name, 'url' => ['/edu/student/topic', 'id' => $topic->id]];
}, $topics);
?>
<?= Menu::widget([
    'options' => ['class' => 'story-category-list'],
    'items' => $items,
]) ?>
