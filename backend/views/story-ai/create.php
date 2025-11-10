<?php

declare(strict_types=1);

use backend\assets\StoryCreateAssistAsset;
use yii\web\View;

/**
 * @var View $this
 * @var string $threadId
 */

StoryCreateAssistAsset::register($this);

$this->title = 'Создать историю (AI)';
?>
<div data-thread-id="<?= $threadId ?>" id="app"></div>
