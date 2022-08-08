<?php

use common\helpers\SmartDate;
use common\models\StoryTest;
use common\models\UserStudent;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $rows
 * @var StoryTest $testing
 * @var UserStudent $student
 */

$this->title = $testing->title . ' - ' . $student->name;

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
<h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/history/list', 'test_id' => $testing->id]) ?> <?= Html::encode($this->title) ?></h1>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Вопрос</th>
            <th>Ответ</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr <?= (int)$row['correct'] === 1 ? '' : 'class="danger"' ?>>
                <td><?= SmartDate::dateSmart($row['question_created'], true) ?></td>
                <td><?= $row['question_name'] ?></td>
                <td><?= $row['user_answers'] ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
