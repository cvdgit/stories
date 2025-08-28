<?php

declare(strict_types=1);

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
?>
<h1 class="page-header"><?= $this->title; ?></h1>
<div class="table-responsive">
    <?= GridView::widget([
        "dataProvider" => $dataProvider,
        "options" => ["class" => "table-responsive"],
        'filterModel' => $filterModel,
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
            [
                'attribute' => 'user_name',
                'filterAttribute' => 'user_id',
                'filter' => $filterUsers,
            ],
        ],
    ]) ?>
</div>
