<?php

use common\models\UserStudent;
use yii\helpers\Html;

/**
 * @var UserStudent $student
 * @var array $rows
 */

$this->title =  $student->name . ' - история обучения';

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
<h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/user/update', 'id' => $student->user_id]) ?> <?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $row): ?>
        <div class="row">
            <div class="col-md-12">
                <h3><?= $row['test_name'] ?></h3>
                <p><?= $row['items_count'] ?> записей в истории (<?= Html::a('показать', ['detail', 'student_id' => $student->id, 'test_id' => $row['test_id']]) ?>)</p>
                <?= Html::a('Очистить историю', ['history/clear', 'student_id' => $student->id, 'test_id' => $row['test_id']], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
        <?php endforeach ?>
        <?php else: ?>
            <p>Нет данных</p>
        <?php endif ?>
    </div>
</div>
