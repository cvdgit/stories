<?php

/* @var $this yii\web\View */

$this->title = 'CVD - Сказки';
?>
<div class="site-index">
    <div class="jumbotron">
        <h1>CVD - Сказки</h1>
    </div>
    <div class="body-content">
        <div class="row">
            <div class="col-lg-4">
            	<?php if (!Yii::$app->user->isGuest) var_dump(Yii::$app->user->can('createStory')); ?>
            </div>
        </div>
    </div>
</div>
