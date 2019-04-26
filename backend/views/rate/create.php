<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Rate */

$this->title = 'Create Rate';
$this->params['breadcrumbs'][] = ['label' => 'Rates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
