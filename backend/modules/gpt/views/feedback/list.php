<?php

declare(strict_types=1);

use backend\assets\MainAsset;
use backend\modules\gpt\Feedback\FeedbackForm;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var FeedbackForm $filterModel
 * @var array $filterUsers
 */

$this->title = "GPT feedback";

MainAsset::register($this);
$this->registerJs(
    $this->renderFile('@backend/modules/gpt/views/feedback/index.js')
);
?>
<h1 class="page-header"><?= $this->title; ?></h1>
<div id="feedback-list" class="table-responsive">
    <?= GridView::widget([
        "dataProvider" => $dataProvider,
        "options" => ["class" => "table-responsive"],
        'filterModel' => $filterModel,
        "columns" => [
            'target',
            [
                'attribute' => 'input',
                'format' => 'raw',
                'value' => static function(array $row) {

                    return '<div class="gpt-input-wrap">
<pre>' . htmlentities($row['input'] ?? '') . '</pre>
<div class="gpt-actions-wrap">
<button data-toggle="tooltip" title="Запустить" type="button" class="gpt-input-action btn gpt-input-run">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
</svg>
</button>
<a title="Изменить промт" target="_blank" class="gpt-input-action btn" href="/admin/index.php?r=llm-prompt/update-by-key-form&key=' . $row['target'] . '">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
</svg>
</a>
</div>
</div>';
                },
            ],
            [
                'attribute' => 'output',
                'format' => 'html',
                'value' => static function(array $row) {
                    return '<pre>' . htmlentities($row['output'] ?? '') . '</pre>';
                },
            ],
            [
                'attribute' => 'score',
                'value' => static function (array $model): string {
                    if ($model['score'] === '1') {
                        return '👍';
                    }
                    return '';
                },
            ],
            'created_at:datetime',
            [
                'attribute' => 'user_name',
                'filterAttribute' => 'user_id',
                'filter' => $filterUsers,
            ],
        ],
    ]) ?>
</div>
