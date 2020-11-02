<?php
/** @var $student common\models\UserStudent */
/** @var $history backend\models\StudentTestHistory */
use yii\helpers\Html;
$this->title =  $student->name . ' - история обучения';
$tests = $history->getStudentTests();
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($tests)): ?>
        <?php foreach ($tests as $test): ?>
        <div class="row">
            <div class="col-md-12">
                <h3><?= $test['title'] ?></h3>
                <p><?= $history->getStudentTestHistoryCount($test['test_id']) ?> записей в истории (<?= Html::a('показать', ['detail', 'student_id' => $student->id, 'test_id' => $test['test_id']]) ?>)</p>
                <?= Html::a('Очистить историю', ['history/clear', 'student_id' => $student->id, 'test_id' => $test['test_id']], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
        <?php endforeach ?>
        <?php else: ?>
            <p>Нет данных</p>
        <?php endif ?>
    </div>
</div>
