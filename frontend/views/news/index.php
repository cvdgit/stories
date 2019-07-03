<?php

use yii\helpers\Url;
use yii\widgets\ListView;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>

<div class="container">
    <div class="row">
        <main class="col-sm-12 col-md-12 site-main" style="margin-top: 0">
            <h1 style="margin-top: 6px; margin-bottom: 33px"><?= $this->getHeader() ?></h1>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}{pager}',
                'itemOptions' => ['class' => 'item'],
                'itemView' => '_view'
            ]) ?>
        </main>
    </div>
</div>
