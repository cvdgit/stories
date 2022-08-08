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

        <table class="table table-bordered table-sm table-hover table-striped">
            <thead>
                <th>Тест</th>
                <th></th>
            </thead>
            <tbody>

            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>

                <tr>
                    <td><?= Html::a($row['test_name'], ['detail', 'student_id' => $student->id, 'test_id' => $row['test_id']]) ?></td>
                    <td><?= Html::a('Очистить историю', ['history/clear', 'student_id' => $student->id, 'test_id' => $row['test_id']], ['class' => 'btn btn-danger']) ?></td>
                </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Нет данных</td>
                </tr>
            <?php endif ?>

            </tbody>
        </table>

    </div>
</div>
