<?php
use yii\helpers\Html;
/** @var $images backend\components\book\blocks\Image[] */
/** @var $texts backend\components\book\blocks\Text[] */
/** @var $tests backend\components\book\blocks\Html[] */
/** @var $transitions backend\components\book\blocks\Transition[] */
/** @var $links backend\components\book\blocks\Link[] */
$haveImages = count($images) > 0;
$haveTexts = count($texts) > 0;
$haveTests = count($tests) > 0;
$haveTransitions = count($transitions) > 0;
$haveLinks = count($links) > 0;
?>
<section>
    <?php if ($haveImages && $haveTexts): ?>
        <div class="row">
            <div class="col-lg-6">
                <?php foreach($images as $image): ?>
                <?php if (!$image->isEmpty()): ?>
                <?= Html::img(null, ['data-src' => $image->image, 'width' => '100%', 'height' => '100%', 'class' => 'lazy']) ?>
                <?php endif ?>
                <?php endforeach ?>
            </div>
            <div class="col-lg-6">
                <?php foreach($texts as $text): ?>
                <?= Html::tag('p', $text->text) ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php else: ?>
        <?php if ($haveImages && !$haveTexts): ?>
        <div class="row">
            <div class="col-lg-6">
                <?php foreach($images as $image): ?>
                    <?= Html::img(null, ['data-src' => $image->image, 'width' => '100%', 'height' => '100%', 'class' => 'lazy']) ?>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>
        <?php if (!$haveImages && $haveTexts): ?>
        <div class="row">
            <div class="col-lg-12">
                <?php foreach($texts as $text): ?>
                    <?= Html::tag('p', $text->text) ?>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>
    <?php endif ?>

    <?php if ($haveTests): ?>
    <?php foreach ($tests as $test): ?>
    <?php if (!$test->isEmpty()): ?>
    <div class="row">
        <div class="col-lg-12">
            <h3><?= $test->header ?></h3>
            <p><?= $test->description ?></p>
            <div class="row">
                <div class="col-lg-offset-3 col-lg-6">
                    <div class="alert alert-success to-slides-tab noselect text-center">
                        <p>Прохождение теста доступно в режиме обуения</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
    <?php endforeach ?>
    <?php endif ?>

    <?php if ($haveTransitions): ?>
    <?php foreach ($transitions as $transition): ?>
        <?php if (!$transition->isEmpty()): ?>
            <div class="row">
                <div class="col-lg-offset-3 col-lg-6">
                    <div class="alert alert-success to-slides-tab noselect text-center">
                        <p>Дополнительный контент (<?= $transition->title ?>) доступен в режиме обучения</p>
                    </div>
                </div>
            </div>
        <?php endif ?>
    <?php endforeach ?>
    <?php endif ?>

    <?php if ($haveLinks): ?>
        <div class="row">
            <div class="col-lg-offset-3 col-lg-6 text-center">
                <h3>Полезные ссылки</h3>
                <ul class="list-inline">
                    <?php foreach ($links as $link): ?>
                        <li><?= Html::a($link->title, $link->href) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    <?php endif ?>
</section>
<hr>