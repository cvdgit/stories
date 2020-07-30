<?php
use yii\helpers\Html;
/** @var $manager backend\components\book\SlideBlocks */
?>
<section>
    <?php if ($manager->haveImages() && $manager->haveTexts()): ?>
        <div class="row">
            <div class="col-lg-6">
                <?php foreach($manager->getImages() as $image): ?>
                <?php if (!$image->isEmpty()): ?>
                <?= Html::img(null, ['data-src' => $image->image, 'width' => '100%', 'height' => '100%', 'class' => 'lazy']) ?>
                <?php endif ?>
                <?php endforeach ?>
            </div>
            <div class="col-lg-6">
                <?php foreach($manager->getTexts() as $text): ?>
                <?= Html::tag('p', $text->text) ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php else: ?>
        <?php if (!$manager->haveTexts() && $manager->haveImages()): ?>
        <div class="row">
            <div class="col-lg-6">
                <?php foreach($manager->getImages() as $image): ?>
                    <?= Html::img(null, ['data-src' => $image->image, 'width' => '100%', 'height' => '100%', 'class' => 'lazy']) ?>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>
        <?php if (!$manager->haveImages() && $manager->haveTexts()): ?>
        <div class="row">
            <div class="col-lg-12">
                <?php foreach($manager->getTexts() as $text): ?>
                    <?= Html::tag('p', $text->text) ?>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>
    <?php endif ?>

    <?php if ($manager->haveTests()): ?>
    <?php foreach ($manager->getTests() as $test): ?>
    <?php if (!$test->isEmpty()): ?>
    <div class="row">
        <div class="col-lg-12">
            <h3>Тест для закрепления материала</h3>
            <p><?= $test->header ?></p>
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

    <?php if ($manager->haveTransitions()): ?>
    <?php foreach ($manager->getTransitions() as $transition): ?>
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

    <?php if ($manager->haveLinks()): ?>
        <div class="row">
            <div class="col-lg-offset-3 col-lg-6 text-center">
                <h3>Полезные ссылки</h3>
                <ul class="list-inline">
                    <?php foreach ($manager->getLinks() as $link): ?>
                        <li><?= Html::a($link->title, $link->href, ['rel' => 'nofollow']) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    <?php endif ?>
</section>
<hr>