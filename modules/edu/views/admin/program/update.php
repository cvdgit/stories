<?php

use modules\edu\widgets\AdminToolbarWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\edu\models\EduProgram */

$this->title = 'Программа: ' . $model->name;
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/program/index']) ?> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
