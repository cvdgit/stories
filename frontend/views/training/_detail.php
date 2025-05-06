<?php

declare(strict_types=1);

use frontend\MentalMap\MentalMap;
use yii\bootstrap\Tabs;
use yii\web\View;

/**
 * @var View $this
 * @var array $data
 * @var array<MentalMap> $mentalMaps
 * @var string $title
 * @var array $quizData
 */

$this->registerCss(<<<CSS
.modal.modal-fullscreen {
  padding: 0 !important;
}
.modal.modal-fullscreen .modal-dialog {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
}
.modal.modal-fullscreen .modal-content {
  height: auto;
  min-height: 100%;
  border: 0 none;
  border-radius: 0;
  box-shadow: none;
}
.modal-body {
    max-height: calc(100vh - 60px);
    overflow-y: auto;
}
.material-tabs {
margin-bottom: 12px;
}
CSS);
?>
<div style="margin-bottom: 8px;"><?= $title ?></div>
<div class="history-nav">
    <?= Tabs::widget([
        'options' => ['class' => 'nav nav-tabs material-tabs'],
        'items' => [
            [
                'label' => 'Ментальные карты',
                'content' => $this->render('_detail_mental_map_content', ['data' => $data, 'mentalMaps' => $mentalMaps],
                ),
                'active' => count($data) > 0,
                'visible' => count($data) > 0,
            ],
            [
                'label' => 'Тесты',
                'content' => $this->render('_detail_quiz_content', ['data' => $quizData]),
                'active' => count($data) === 0 && count($quizData) > 0,
                'visible' => count($quizData) > 0,
            ],
        ],
    ]) ?>
</div>

