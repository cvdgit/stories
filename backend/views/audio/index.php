<?php

declare(strict_types=1);

use common\models\Story;
use common\models\StoryAudioTrack;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Story $model
 * @var DataProviderInterface $dataProvider
 * @var array $sidebarMenuItems
 * @var array $breadcrumbs
 * @var string $title
 */

$this->params = array_merge($this->params, $sidebarMenuItems);
$this->params = array_merge($this->params, $breadcrumbs);
$this->title = $title;
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title); ?></h1>
    <p>
        <?= Html::a('Создать дорожку', ['create', 'story_id' => $model->id], ['class' => 'btn btn-success']); ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'name',
            [
                'attribute' => 'type',
                'value' => static function($model) {
                    return $model->audioTypeValue();
                },
            ],
            [
                'attribute' => 'default',
                'value' => static function($model) {
                    return $model->isDefault() ? 'Да' : 'Нет';
                }
            ],
            'user.username',
            [
                'attribute' => 'status',
                'value' => static function($model) {
                    if ($model->isOriginal()) {
                        return $model->getStatusText();
                    }
                    return '';
                },
                'filter' => StoryAudioTrack::getStatusArray(),
            ],
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
