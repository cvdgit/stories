<?php

use common\models\News;
use yii\bootstrap\Nav;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $this yii\web\View */
/** @var $status integer */

$this->title = 'Управление публикациями';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<div class="row news-index">
    <div class="col-xs-12">
        <?= Nav::widget([
            'options' => ['class' => 'nav nav-tabs material-tabs'],
            'items' => [
                [
                    'label' => News::statusLabel(News::STATUS_PROPOSED),
                    'url' => ['news/admin', 'status' => News::STATUS_PROPOSED],
                    'active' => (int)$status === News::STATUS_PROPOSED,
                ],
                [
                    'label' => News::statusLabel(News::STATUS_REJECTED),
                    'url' => ['news/admin', 'status' => News::STATUS_REJECTED],
                    'active' => (int)$status === News::STATUS_REJECTED,
                ],
                [
                    'label' => News::statusLabel(News::STATUS_PUBLISHED),
                    'url' => ['news/admin', 'status' => News::STATUS_PUBLISHED],
                    'active' => (int)$status === News::STATUS_PUBLISHED,
                ],
            ],
        ]) ?>
        <div style="padding: 20px 0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'columns' => [
                    'title',
                    'slug',
                    [
                        'attribute' => 'user_id',
                        'value' => 'user.username',
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => 'created_at',
                        'format' => 'datetime',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'buttons' => [
                            'view' => function($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"></span>',
                                    Yii::$app->urlManagerFrontend->createAbsoluteUrl(['news/view', 'slug' => $model->slug]),
                                    ['target' => '_blank']);
                            }
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>