<?php

use modules\edu\models\EduClassProgram;
use modules\edu\models\EduTopic;
use modules\edu\widgets\AdminToolbarWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var EduTopic $model
 * @var EduClassProgram $classProgram
 */

$this->title = 'Создать тему';

$this->params['breadcrumbs'] = [
    [
        'label' => $classProgram->class->name . ' - ' . $classProgram->program->name,
        'url' => ['/edu/admin/class-program/update', 'id' => $classProgram->id],
    ],
    $this->title,
];
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/class-program/update', 'id' => $classProgram->id]) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="row">
        <div class="col-lg-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
