<?php

declare(strict_types=1);

use backend\widgets\SelectUserWidget;
use modules\edu\Teacher\ClassBook\TeacherAccess\TeacherAccessForm;
use modules\edu\Teacher\ClassBook\TeacherAccess\UserItem;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var TeacherAccessForm $formModel
 * @var array<array-key, UserItem> $teachers
 * @var string $accessJson
 * @var int $classBookId
 */

$this->registerJs("window.teacherAccessItems = $accessJson");
$this->registerJs("window.classBookId = $classBookId");
?>
<?php
$form = ActiveForm::begin([
    'id' => 'teacher-access-form',
    'action' => ['/edu/teacher/class-book/grant-access'],
    'options' => ['style' => 'margin-bottom: 20px'],
]); ?>
<div style="display: flex; flex-direction: row; justify-content: space-between">
    <div style="flex: 1; margin-right: 20px;">
        <?= $form->field($formModel, 'teacher_id')->widget(SelectUserWidget::class, [
            'userModels' => $teachers,
        ]) ?>
    </div>
    <div class="form-group" style="display: flex; align-items: end">
        <?= $form->field($formModel, 'class_book_id')->hiddenInput()->label(false) ?>
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-small']) ?>
    </div>
</div>
<?php
ActiveForm::end(); ?>
<h3 class="h4">Учителя, у которых есть доступ к классу:</h3>
<table class="table table-bordered table-hover" id="teacher-list">
    <thead>
    <tr>
        <th>Учитель</th>
        <th></th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
