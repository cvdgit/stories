<?php
/** @var $model common\models\Story */
/** @var $student common\models\UserStudent */
/** @var $category common\models\Category */
use common\components\StoryCover;
use common\models\test\SourceType;
use yii\helpers\Html;
?>
<div class="row" style="padding: 10px 0">
    <div class="col-lg-2 col-md-3 col-sm-3 info">
        <?= Html::img(StoryCover::getListThumbPath($model->cover), ['style' => 'max-width: 100%; height: auto']) ?>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-9 clearfix">
        <div class="row row-no-gutters">
            <div class="col-md-7">
                <h3 style="margin-top:0"><?= Html::a($model->title, $model->getStoryUrl()) ?></h3>
                <?php foreach($model->tests as $test): ?>
                    <div style="margin-bottom: 10px">
                        <?php if (SourceType::isTest($test)): ?>
                        <p>
                            <?= Html::a('<i class="glyphicon glyphicon-phone"></i> Мобильная версия', ['test-mobile/view', 'id' => $test->id]) ?>
                        </p>
                        <?php endif ?>
                        <p>
                            <?= Html::a('<i class="glyphicon glyphicon-play-circle"></i> ' . $test->header, $test->getRunUrl(), ['class' => 'run-test']) ?>
                        </p>
                        <div class="clearfix">
                            <?= Html::a('<i class="glyphicon glyphicon-trash"></i>', ['test/clear-history', 'category_id' => $category->id, 'student_id' => $student->id, 'test_id' => $test->id], ['title' => 'Очистить прогресс', 'style' => 'float: right']) ?>
                            <div class="progress">
                                <div data-test-id="<?= $test->id ?>" data-student-id="<?= $student->id ?>" class="progress-bar" role="progressbar" style="width: <?= $student->getProgress($test->id) ?>%;min-width: 20px">
                                    <?= $student->getProgress($test->id) ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>
