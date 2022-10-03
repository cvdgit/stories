<?php

declare(strict_types=1);

use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduTopic;
use modules\edu\widgets\StudentToolbarWidget;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var View $this
 * @var UserStudent $student
 * @var DataProviderInterface $dataProvider
 * @var EduTopic[] $topics
 * @var string $classProgramName
 * @var EduTopic $topic
 */

$this->title = $student->name;

$this->registerCss(<<<CSS
.lesson-item {
    position: relative;
    width: 200px;
    padding: 0;
    box-sizing: border-box;
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    -webkit-box-align: center;
    align-items: center;
    z-index: 1;
}
.lesson-item__wrap {
    margin-bottom: 1rem;
    text-decoration: none;
}
.lesson-item__wrap:hover .lesson-name span {
    color: #99cd50 !important;
}
.lesson-image {
    position: relative;
    top: 0;
    left: 0;
    width: 100px;
    height: 100px;
}
.lesson-image__img {
    width: 100px;
    height: 100px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.lesson-name {
    color: rgba(9, 21, 38, 0.85);
    text-align: center;
    width: 100%;
    margin-top: 16px;
}
.lesson-name__inner {
    max-width: 100%;
    display: inline-block;
    color: inherit;
    font-weight: normal;
    font-style: normal;
    transform: rotate(0.03deg);
    font-size: 16px;
    line-height: 22px;
}
CSS
);
?>
<div class="container">
    <?= StudentToolbarWidget::widget(['student' => $student]) ?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="h2" style="margin-top: 0; margin-bottom: 20px"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/student/index']) ?> <?= $classProgramName ?></h1>
        </div>
    </div>

    <div class="row" style="margin-bottom: 40px">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <?= $this->render('_all_topics', ['topics' => $topics, 'currentTopicId' => $topic->id]) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top:0">

            <h2 class="h3" style="margin-top:0;margin-bottom:5rem"><?= Html::encode($topic->name) ?></h2>

            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'itemView' => '_lesson_item',
                'itemOptions' => ['tag' => false],
                'viewParams' => ['studentId' => $student->id],
                'layout' => "{summary}\n<div class=\"row display-flex\">{items}</div>\n{pager}",
            ]) ?>

        </div>
    </div>
</div>
