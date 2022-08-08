<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var $test common\models\StoryTest
 * @var $students array
 * @var View $this
 */

$this->title = 'История прохождения - ' . $test->title;

$this->registerCss(<<<CSS
.back-arrow {
  background-color: #eee;
  padding: 6px;
  border-radius: 50%;
  font-size: 20px;
}
CSS
);
?>
<h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/test/update', 'id' => $test->id]) ?> <?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($students)): ?>
            <?= Html::a('Очистить по всем студентам', ['/history/clear-all', 'test_id' => $test->id], ['class' => 'btn btn-danger']) ?>
            <?php foreach ($students as $student): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3><?= $student['student_name'] ?></h3>
                        <p><?= Html::a('История', ['/history/history', 'testing_id' => $test->id, 'student_id' => $student['student_id']]) ?></p>
                        <? //Html::a('Очистить историю', ['history/clear', 'student_id' => $student->id, 'test_id' => $test->id], ['class' => 'btn btn-danger']) ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <p>Нет данных</p>
        <?php endif ?>
    </div>
</div>
