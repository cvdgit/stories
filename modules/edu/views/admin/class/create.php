<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\edu\models\EduClass */

$this->title = 'Создать класс';
$this->params['breadcrumbs'][] = ['label' => 'Классы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
