<?php

declare(strict_types=1);

use common\models\StoryTest;
use common\models\UserStudent;
use yii\helpers\Html;

/**
 * @var $detail array
 * @var UserStudent $student
 * @var StoryTest $test
 */

$this->title = 'Ученик: ' . $student->name . ' / Тестирование: ' . $test->title;

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
<h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/history/view', 'id' => $student->id]) ?> <?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ИД вопроса</th>
                    <th>Вопрос</th>
                    <th>Ответ пользователя</th>
                    <th>Кол-во звезд</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($detail as $row): ?>
                <tr>
                    <td><?= $row['question_id'] ?></td>
                    <td><?= $row['entity_name'] ?></td>
                    <td><?= $row['answer_entity_name'] ?></td>
                    <td><?= $row['stars'] ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
