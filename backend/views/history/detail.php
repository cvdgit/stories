<?php
use yii\helpers\Html;
/** @var $detail array */
/** @var $student common\models\UserStudent */
/** @var $test common\models\StoryTest */
$this->title = 'Детализация истории ответов: ' . $student->name . ' - ' . $test->title;
$this->params['sidebarMenuItems'] = [
    ['label' => $student->name . ' - история обучения', 'url' => ['view', 'id' => $student->id]],
];
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>question_id</th>
                    <th>entity_name</th>
                    <th>Ответ пользователя</th>
                    <th>stars</th>
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
