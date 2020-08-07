<?php
use backend\components\BlockRenderer;
use yii\helpers\Html;
/** @var $manager backend\components\book\SlideBlocks */
?>
<section>

    <?php if (!$manager->videos->isEmpty()): ?>
        <div class="row">
            <div class="col-lg-6">
                <?= BlockRenderer::renderVideos($manager->videos) ?>
            </div>
            <?php if (!$manager->texts->isEmpty()): ?>
            <div class="col-lg-6">
                <?= BlockRenderer::renderTexts($manager->texts) ?>
            </div>
            <?php endif ?>
        </div>
    <?php else: ?>
        <?php if (!$manager->images->isEmpty() && !$manager->texts->isEmpty()): ?>
            <div class="row">
                <div class="col-lg-6">
                    <?= BlockRenderer::renderImages($manager->images) ?>
                </div>
                <div class="col-lg-6">
                    <?= BlockRenderer::renderTexts($manager->texts) ?>
                </div>
            </div>
        <?php else: ?>
            <?php if ($manager->texts->isEmpty() && !$manager->images->isEmpty()): ?>
                <div class="row">
                    <div class="col-lg-6">
                        <?= BlockRenderer::renderImages($manager->images) ?>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($manager->images->isEmpty() && !$manager->texts->isEmpty()): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?= BlockRenderer::renderTexts($manager->texts) ?>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>

    <?php if (!$manager->htmltests->isEmpty()): ?>
        <?php foreach ($manager->htmltests as $test): ?>
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

    <?php if (!$manager->tests->isEmpty()): ?>
        <?php foreach ($manager->tests as $test): ?>
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

    <?php if (!$manager->transitions->isEmpty()): ?>
    <?php foreach ($manager->transitions as $transition): ?>
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

    <?php if (!$manager->links->isEmpty()): ?>
        <div class="row">
            <div class="col-lg-offset-3 col-lg-6 text-center">
                <h3>Полезные ссылки</h3>
                <ul class="list-inline">
                    <?php foreach ($manager->links as $link): ?>
                        <li><?= Html::a($link->title, $link->href, ['rel' => 'nofollow', 'target' => '_blank']) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    <?php endif ?>
</section>
<hr>