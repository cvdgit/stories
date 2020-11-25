<?php
/** @var $model common\models\Story */
/** @var $student common\models\UserStudent */
use common\components\StoryCover;
use yii\helpers\Html;
?>
<div class="row" style="padding: 10px 0">
    <div class="col-lg-2 col-md-3 col-sm-3 info">
        <?= Html::img(StoryCover::getListThumbPath($model->cover), ['style' => 'max-width: 100%; height: auto']) ?>
    </div>
    <div class="col-lg-7 col-md-9 col-sm-9 clearfix">
        <h3 style="margin-top:0"><?= $model->title ?></h3>
        <?php foreach($model->tests as $test): ?>
        <p><?= $test->title ?></p>
        <p><?= $student->getProgress($test->id) ?></p>
        <?php endforeach ?>
    </div>
</div>
