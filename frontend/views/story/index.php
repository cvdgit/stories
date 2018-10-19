<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use xj\holder\HolderAssets;

HolderAssets::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\StorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Каталог историй';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-3 col-offset-1">sidebar</div>
    <div class="col-sm-8">
        <div class="story-index">
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="row">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['tag' => false],
                'summary' => '',
                'itemView' => '_storyitem',
            ]) ?>
            </div>
        </div>
    </div>
</div>