<?php

declare(strict_types=1);

use backend\LlmPrompt\LlmPrompt;
use backend\LlmPrompt\LlmPromptListFilterModel;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var LlmPromptListFilterModel $filterModel
 */

$this->title = 'Промты';
?>

<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2"><?= $this->title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <a class="btn btn-primary" href="<?= Url::to(['/llm-prompt/create-form']) ?>">Создать промт</a>
        </div>
    </div>
</div>

<div>
    <?= GridView::widget([
        'options' => ['class' => 'table-responsive'],
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => [
            'id',
            'name',
            'key',
            [
                'attribute' => 'prompt',
                'format' => 'html',
                'value' => static function(LlmPrompt $model) {
                    return '<pre>' . htmlentities($model->prompt ?? '') . '</pre>';
                },
            ],
            'created_at:datetime',
        ],
    ]) ?>
</div>
