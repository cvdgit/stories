<?php
/** @var $model common\models\Story */
/** @var $student common\models\UserStudent */
/** @var $category common\models\Category */
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
        <p>Прогресс (<?= $student->getStudentName() ?>): <?= $student->getProgress($test->id) ?>%</p>
        <p>
            <?= Html::a('<i class="glyphicon glyphicon-play-circle"></i> ' . $test->header,
                $test->getRunUrl(),
                ['class' => 'run-test']) ?>
            | <?= Html::a('<i class="glyphicon glyphicon-picture"></i> Перейти к истории',
                $model->getStoryUrl()) ?>
            | <?= Html::a('Очистить историю', ['test/clear-history', 'category_id' => $category->id, 'student_id' => $student->id, 'test_id' => $test->id]) ?>
        </p>
        <?php endforeach ?>
    </div>
</div>
