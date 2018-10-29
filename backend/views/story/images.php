<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $images [] */

$this->title = 'Изображения истории';
$this->params['breadcrumbs'][] = ['label' => 'Истории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
    <?php foreach ($images as $imagePath): ?>
        <div class="col-xs-6 col-md-3">
            <a href="#" class="thumbnail"><img src="<?= $imagePath ?>" /></a>
        </div>
    <?php endforeach ?>
    </div>
</div>