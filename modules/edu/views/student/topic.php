<?php

declare(strict_types=1);

use common\models\UserStudent;
use modules\edu\models\EduTopic;
use modules\edu\widgets\StudentToolbarWidget;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\widgets\ListView;

/**
 * @var UserStudent $student
 * @var DataProviderInterface $dataProvider
 * @var EduTopic[] $topics
 * @var string $classProgramName
 */

$this->title = $student->name;
?>
<div class="container">

    <?= StudentToolbarWidget::widget(['student' => $student]) ?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="h2" style="margin-top: 0; margin-bottom: 20px"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/student/index']) ?> <?= $classProgramName ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <?= $this->render('_all_topics', ['topics' => $topics]) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top:0">

            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'itemView' => '_lesson_item',
                'itemOptions' => ['tag' => false],
                'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
            ]) ?>

        </div>
    </div>
</div>
