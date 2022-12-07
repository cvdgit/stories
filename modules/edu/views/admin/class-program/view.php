<?php

declare(strict_types=1);

use modules\edu\models\EduClassProgram;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var EduClassProgram $classProgram
 */

$this->title = 'Программа обучения';

$this->params['breadcrumbs'] = [
    [
        'label' => 'Программы обучения',
        'url' => ['/edu/admin/class-program/index'],
    ],
];

$this->registerCss(<<<CSS
.edu-topic {
    margin-bottom: 30px;
    border-bottom: 1px #eee solid;
}
.edu-topic-header {
    margin-bottom: 20px;
}
.edu-lesson {
    margin-bottom: 20px;
}
CSS
);
?>
<div>
    <h1 class="page-header"><?= $classProgram->class->name . ' / ' . $classProgram->program->name; ?></h1>
    <div>
        <?php foreach ($classProgram->eduTopics as $topic): ?>
        <div class="edu-topic">
            <h3 class="h4 edu-topic-header">Тема: <?= $topic->name; ?></h3>
            <div>
                <?php foreach ($topic->eduLessons as $lesson): ?>
                <div class="edu-lesson">
                    <h4 class="h5">Урок: <?= $lesson->name; ?></h4>
                    <div>
                        <div class="row" style="display: flex; flex-wrap: wrap">
                        <?php foreach ($lesson->stories as $story): ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <a target="_blank" href="<?= Yii::$app->urlManagerFrontend->createUrl(['/edu/story/view', 'id' => $story->id, 'program_id' => $classProgram->id]); ?>" class="thumbnail" style="border: 0 none; margin-bottom: 0">
                                        <?= Html::img($story->getListThumbPath()); ?>
                                        <div><?= $story->title; ?></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
