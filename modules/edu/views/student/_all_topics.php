<?php

declare(strict_types=1);

use modules\edu\models\EduTopic;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var EduTopic[] $topics
 * @var int $currentTopicId
 */

$this->registerCss(<<<CSS
.topic-list {
    border-radius: 16px;
    box-shadow: rgb(57 78 127 / 20%) 0 4px 8px;
    transition: transform 125ms ease-in-out 0s, box-shadow 125ms ease-in-out 0s;
    margin-bottom: 20px;
}
.topic-list-item {
    border: 0 none;
    padding: 12px 16px 16px;
}
.topic-list-item:first-child {
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
}
.topic-list-item:last-child {
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
}
.topic-list-item.active,
.topic-list-item.active:hover {
    background-color: #99cd50;
}
CSS
);
?>
<div class="list-item topic-list">
<?php foreach ($topics as $topic): ?>
    <?= Html::a($topic->name, ['/edu/student/topic', 'id' => $topic->id], ['class' => 'list-group-item topic-list-item' . ($topic->id === $currentTopicId ? ' active' : '')]); ?>
<?php endforeach; ?>
</div>
