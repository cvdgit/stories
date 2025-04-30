<?php

declare(strict_types=1);

use modules\edu\models\EduClassBook;
use modules\edu\Teacher\ClassBook\TeacherAccess\EduClassBookTeacherAccess;
use modules\edu\widgets\grid\ArrowColumn;
use modules\edu\widgets\TeacherMenuWidget;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Мои классы';
$this->registerJs($this->renderFile('@modules/edu/views/teacher/class-book/_program-list.js'));
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::encode($this->title) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить класс', ['/edu/teacher/class-book/create'], ['class' => 'btn btn-small']) ?>
            </div>
        </div>
    </div>

    <div id="class-book-list">
        <?php Pjax::begin(['id' => 'pjax-class-books']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                'name',
                [
                    'attribute' => 'class.name',
                    'label' => 'Класс',
                ],
                [
                    'label' => 'Темы',
                    'format' => 'raw',
                    'value' => static function(EduClassBook $classBook): string {
                        return Html::a('Настроить', ['/edu/teacher/class-book/manage-topics', 'class_book_id' => $classBook->id], ['class' => 'manage-topics']);
                    }
                ],
                [
                    'label' => 'Учеников',
                    'attribute' => 'studentCount',
                ],
                [
                    'label' => 'Доступ',
                    'format' => 'raw',
                    'value' => static function(EduClassBook $classBook): string {
                        $names = array_map(static function(EduClassBookTeacherAccess $access): string {
                            return $access->teacher->getProfileName();
                        }, $classBook->getAccessTeachers()->with('teacher')->all());
                        return (count($names) > 0 ? implode(', ', $names) . ' ' : '') . Html::a('<i class="glyphicon glyphicon-tasks" style="pointer-events: none"></i>', ['/edu/teacher/class-book/teacher-access', 'class_book_id' => $classBook->id], ['class' => 'teacher-access', 'data-pjax' => '0', 'title' => 'Настроить', 'style' => 'color: black']);
                    }
                ],
                [
                    'class' => ArrowColumn::class,
                    'url' => static function($model) {
                        return ['/edu/teacher/class-book/students', 'id' => $model->id];
                    },
                ],
            ],
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>
