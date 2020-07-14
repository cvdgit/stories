<?php
use common\models\UserQuestionHistoryModel;
/* @var $this yii\web\View */
/* @var $students common\models\UserStudent[] */
$title = 'Прогресс обучения';
$this->setMetaTags($title,
    $title,
    '',
    $title);
$historyModel = new UserQuestionHistoryModel();
?>
<h1>Прогресс обучения</h1>

<!--h4>Тесты</h4-->
<div class="user-question-history">
<?php $haveHistory = false; ?>
<?php foreach ($students as $student): ?>
    <?php
    $historyModel->student_id = $student->id;
    $animalsData = $historyModel->getUserAnimalsData(1);
    $haveAnimalsData = count($animalsData) > 0;
    $continentsData = $historyModel->getUserContinentsData(2);
    $haveContinentsData = count($continentsData) > 0;
    ?>
    <?php if ($haveAnimalsData || $haveContinentsData): ?>
        <?php $haveHistory = true; ?>

    <h3><?= $student->name ?> знает</h3>
    <?php if ($haveAnimalsData): ?>
    <div class="user-question-history-block">
        <blockquote>
            <p>Кто где живет?</p>
        </blockquote>
        <?php $progress = $student->getProgress(1); ?>
        <?php if ($progress > 0): ?>
        <div class="row row-no-gutters">
            <div class="wikids-progress col-md-6" style="height: 20px">
                <div class="progress-bar progress-bar-info" style="width: <?= $progress ?>%;"><?= $progress ?>%</div>
            </div>
        </div>
        <?php endif ?>
        <?php foreach ($animalsData as $item): ?>
            <p><?= $item['entity_name'] ?> живет на континенте <?= $item['answer_entity_name'] ?></p>
        <?php endforeach ?>
    </div>
    <?php endif ?>
    <?php if ($haveContinentsData): ?>
    <div class="user-question-history-block">
        <blockquote>
            <p>Кто обитает на континенте?</p>
        </blockquote>
        <?php $progress = $student->getProgress(2); ?>
        <?php if ($progress > 0): ?>
            <div class="row row-no-gutters">
                <div class="wikids-progress col-md-6" style="height: 20px">
                    <div class="progress-bar progress-bar-info" style="width: <?= $progress ?>%;"><?= $progress ?>%</div>
                </div>
            </div>
        <?php endif ?>
        <?php foreach ($continentsData as $item): ?>
            <p>На континенте <?= $item['entityName'] ?> обитает <?= $item['number_animals'] ?> животных</p>
        <?php endforeach ?>
    </div>
    <?php endif ?>

    <?php endif ?>
<?php endforeach ?>
<?php if (!$haveHistory): ?>
    <h4>Нет истории обучения</h4>
<?php endif ?>
</div>