<?php

use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $model modules\edu\models\EduTopic
 * @var $lessonsDataProvider DataProviderInterface
 */
$this->title = 'Тема: ' . $model->name;
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/topic/index']) ?> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-lg-4">
            <div style="margin-bottom: 20px">
                <?= Html::a('Создать урок', ['/edu/admin/lesson/create', 'topic_id' => $model->id], ['class' => 'btn btn-primary']) ?>
            </div>
            <?= GridView::widget([
                'dataProvider' => $lessonsDataProvider,
                'summary' => false,
                'columns' => [
                    [
                        'attribute' => 'name',
                        'enableSorting' => false,
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{update} {delete}',
                        'urlCreator' => static function($action, $model, $key, $index) {
                            return Url::to(['/edu/admin/lesson/' . $action, 'id' => $model->id]);
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
