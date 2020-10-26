<?php
/** @var $test common\models\StoryTest */
/** @var $students common\models\UserStudent[] */

use backend\models\StudentTestHistory;
use yii\helpers\Html;
$this->title =  $test->title . ' - история обучения';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($students)): ?>
            <?= Html::a('Очистить по всем студентам', ['history/clear-all', 'test_id' => $test->id], ['class' => 'btn btn-danger']) ?>
            <?php foreach ($students as $student): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3><?= $student->name ?></h3>
                        <p><?= (new StudentTestHistory($student->id))->getStudentTestHistoryCount($test->id) ?> записей в истории</p>
                        <? //Html::a('Очистить историю', ['history/clear', 'student_id' => $student->id, 'test_id' => $test->id], ['class' => 'btn btn-danger']) ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <p>Нет данных</p>
        <?php endif ?>
    </div>
</div>
