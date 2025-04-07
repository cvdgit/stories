<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = "GPT feedback";
?>
<h1 class="page-header"><?= $this->title; ?></h1>
<?= GridView::widget([
    "dataProvider" => $dataProvider,
    "options" => ["class" => "table-responsive"],
    "columns" => [
        'target',
        'input',
        'output',
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
        'user_name'
    ],
]); ?>
