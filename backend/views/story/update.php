<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = 'История: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias])];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div id="alert_placeholder"></div>
<div class="story-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Изображения', ['story/images', 'id' => $model->id]) ?></p>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
