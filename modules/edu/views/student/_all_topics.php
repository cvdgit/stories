<?php

declare(strict_types=1);

use modules\edu\models\EduTopic;
use yii\widgets\Menu;

/**
 * @var EduTopic[] $topics
 * @var int $currentTopicId
 */

$items = array_map(static function(EduTopic $topic) use ($currentTopicId) {
    return [
        'label' => $topic->name,
        'url' => ['/edu/student/topic', 'id' => $topic->id],
        'active' => $topic->id === $currentTopicId,
    ];
}, $topics);
?>
<?= Menu::widget([
    'options' => ['class' => 'story-category-list'],
    'items' => $items,
]) ?>
