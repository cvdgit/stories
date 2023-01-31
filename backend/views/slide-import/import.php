<?php

declare(strict_types=1);

use backend\assets\WikidsRevealAsset;
use backend\widgets\SelectStoryWidget;
use yii\web\View;

/**
 * @var View $this
 * @var int $storyId
 */

WikidsRevealAsset::register($this);

$this->registerJs($this->renderFile('@backend/views/slide-import/_import.js'));
$this->registerCss($this->renderFile('@backend/views/slide-import/_import.css'));
?>
<div style="min-height: 50rem">
    <div style="display: flex; flex-direction: row">
        <div>Из истории:</div>
        <div class="col-md-8">
            <?= SelectStoryWidget::widget([
                'id' => 'select-story-slides',
                'onChange' => 'onStoryChange',
                'showRecentStories' => true,
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div id="import-slides-list" data-to-story-id="<?= $storyId; ?>"></div>
        </div>
        <div class="col-md-4">
            <p>Выбрано слайдов: <strong id="import-slides-count">0</strong></p>
            <div class="checkbox">
                <label>
                    <input name="slides" type="checkbox" id="import-slides-delete">
                    Удалить выбранные слайды из истории после импорта
                </label>
            </div>
            <button type="button" id="import-slides" class="btn btn-primary">Импортировать</button>
        </div>
    </div>
</div>
