<?php

declare(strict_types=1);

use modules\edu\models\EduClassProgram;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var EduClassProgram $classProgram
 * @var array<int, array<array-key, string>> $lessonAccess
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

$this->registerJs($this->renderFile('@modules/edu/views/admin/class-program/_view.js'));
?>
<div style="position: relative; width: 100%">
<div id="lesson-list">
    <h1 class="page-header"><?= $classProgram->class->name . ' / ' . $classProgram->program->name; ?></h1>
    <div>
        <?php foreach ($classProgram->eduTopics as $topic): ?>
        <div class="edu-topic">
            <h3 class="h4 edu-topic-header">Тема: <?= $topic->name; ?></h3>
            <div>
                <?php foreach ($topic->eduLessons as $lesson): ?>
                <div class="edu-lesson">
                    <div style="display: flex; flex-direction: row; align-items: center">
                        <h4 class="h5">Урок: <?= $lesson->name; ?></h4>
                        <div style="margin: 0 20px">|</div>
                        <?= Html::dropDownList('lesson' . $lesson->id, $lessonAccess[$lesson->id] ?? null, ['access' => 'Доступен', 'deny' => 'Недоступен'], ['class' => 'form-control', 'data-lesson-id' => $lesson->id, 'style' => 'width: auto', 'prompt' => 'Автоматический доступ']); ?>
                    </div>
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

    <div id="controls" style="position: fixed; width: 600px; left: 0; right: 0; translate: calc(50vw - 50%); bottom: 40px; display: none">
        <div style="width: 80%; max-width: 600px; margin: 0 auto; padding: 10px; color: #fff; background: #eee; border-radius: 6px; box-shadow: 0 1px 0 #ddd; overflow: hidden; pointer-events: auto;">
            <div style="display: flex; align-items: center; justify-content: center">
                <button data-action="<?= Url::to(['/edu/admin/lesson/save-access', 'id' => $classProgram->id]); ?>" class="btn btn-primary">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>
