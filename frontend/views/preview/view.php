<?php
use common\helpers\Url;
use frontend\widgets\PreviewRevealWidget;
use yii\helpers\Html;
/** @var $model common\models\Story */
$title = $model->title;
$this->setMetaTags($title, $model->description, $title, $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container story-head-container">
    <main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner" id="story-container">
                <?= PreviewRevealWidget::widget(['model' => $model]) ?>
            </div>
        </div>
    </main>
</div>
<div class="container">
    <main class="site-story-main-descr">
        <?= $this->render('/story/_story_main_block', ['model' => $model]) ?>
        <div class="story-description" style="margin-top: 10px">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-12">
                    <div class="story-tags">
                        <!--noindex-->
                        <?php foreach($model->tags as $tag): ?>
                            <?= '#' . Html::a($tag->name, ['tag', 'tag' => $tag->name], ['rel' => 'nofollow']) ?>
                        <?php endforeach ?>
                        <!--/noindex-->
                    </div>
                    <div class="story-date"><span>Опубликована:</span> <?= \common\helpers\SmartDate::dateSmart($model->published_at, true) ?></div>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12">
                </div>
            </div>
        </div>
    </main>
</div>