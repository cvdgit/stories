<?php

declare(strict_types=1);

use modules\edu\forms\admin\StudentSearch;
use modules\edu\models\EduStudent;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var StudentSearch $searchModel
 */

$this->title = 'Управление учениками';
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
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => static function(EduStudent $model) {
                    return Html::a($model->name, ['/edu/admin/student/stories', 'student_id' => $model->id]);
                },
            ],
            'user.email:email:Email',
            'class.name',
            'created_at:datetime',
            'user.last_activity:datetime:Активность',
            [
                'class' => ActionColumn::class,
                'template' => '{delete}',
            ],
        ],
    ]) ?>
</div>
