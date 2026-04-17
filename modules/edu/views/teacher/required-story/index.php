<?php

declare(strict_types=1);

use modules\edu\models\EduStudent;
use modules\edu\widgets\TeacherMenuWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var EduStudent[] $students
 * @var int|null $activeStudentId
 * @var int $todayPlan
 * @var int $todayFact
 */

$this->title = 'Обязательные истории';
$this->registerCss($this->renderFile('@modules/edu/views/teacher/required-story/style.css'));
$this->registerJs($this->renderFile('@modules/edu/views/teacher/required-story/index.js'));
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::encode($this->title) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить', ['/edu/teacher/required-story/create'], ['class' => 'btn btn-small required-story-create']) ?>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" style="display: flex; flex-direction: row; gap: 20px; align-items: center; justify-content: center">
        <li class="<?= $activeStudentId === null ? 'active' : '' ?>">
            <a href="<?= Url::to(['/edu/teacher/required-story/index']) ?>">По историям</a>
        </li>
        <?php foreach ($students as $student): ?>
        <li class="<?= $student->id === $activeStudentId ? 'active' : '' ?>">
            <a href="<?= Url::to(['/edu/teacher/required-story/index', 'studentId' => $student->id]) ?>"><?= $student->name ?></a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div id="required-stories-wrap" class="table-responsive" style="margin-bottom: 50px;">
        <?php Pjax::begin(['id' => 'pjax-required-stories']); ?>
        <div style="padding: 20px 10px; display: flex; justify-content: center">
            <div style="border: 1px #808080 solid; padding: 10px 20px; border-radius: 10px; display: flex; flex-direction: column; gap: 8px">
                <h2 class="h3" style="margin: 0;">На сегодня</h2>
                <div>План: <b><?= $todayPlan ?></b></div>
                <div>Факт: <b><?= $todayFact ?></b></div>
            </div>
        </div>
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'itemView' => '_required_story_row',
            'itemOptions' => ['class' => 'required-story-row'],
            'layout' => <<<HTML
{summary}
<div class="required-stories">
<div class="required-story-row required-story-row-header">
<div class="required-story-cell">Статус</div>
<div class="required-story-cell">История</div>
<div class="required-story-cell">Прогресс</div>
<div class="required-story-cell">Дата начала</div>
<div class="required-story-cell">Дата создания</div>
<div class="required-story-cell">Действия</div>
</div>
{items}
</div>
HTML,
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>
