<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var string $learningPathId
 */

$this->registerCss(
    <<<CSS

*,:after,:before {
    box-sizing: border-box
}

html {
    height: 100%;
}

body {
    height: 100%;
    min-height: 100%;
    position: relative;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.lp-container {
    display: flex;
    flex-direction: row;
    column-gap: 30px;
}
.lp-column {
    min-width: 300px;
}
.lp-column-title {
    margin-bottom: 10px;
    font-weight: 500;
    min-height: 40px;
    max-height: 40px;
}
.lp-column-item {
    margin-bottom: 20px;
}
.lp-column-item-children:not(:empty) {
    margin: 20px 0;
}
CSS
);

$this->title = 'Карта знаний';
$this->registerJs($this->renderFile('@frontend/modules/LearningPath/views/default/index.js'));
?>
<div class="container" style="height: 100%">
    <div style="width: 100%; height: 100%" data-learning-path-id="<?= $learningPathId ?>"></div>
</div>
