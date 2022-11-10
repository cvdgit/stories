<?php

declare(strict_types=1);

use modules\edu\forms\admin\StudentStoriesSearch;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var StudentStoriesSearch $searchModel
 * @va
 */

$this->title = 'Истории, просмотренные учеником';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'summary' => false,
        'filterModel' => $searchModel,
        'columns' => [
            'story_title:text:История',
            'updated_at:datetime:Обновлено',
            [
                'format' => 'raw',
                'value' => static function($item) {
                    return Html::a('Очистить', ['/edu/admin/student/clear-story-history', 'student_id' => $item['student_id'], 'story_id' => $item['story_id']], ['onclick' => "return confirm('Подтверждаете?')"]);
                },
            ],
        ],
    ]) ?>
</div>
