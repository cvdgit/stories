<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\edu\models\EduClassProgram */

$this->title = 'Программа обучения: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Программы обучения', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-8">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
