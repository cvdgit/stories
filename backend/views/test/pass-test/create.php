<?php

declare(strict_types=1);

use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use backend\models\pass_test\PassTestForm;

/**
 * @var StoryTest $quizModel
 * @var PassTestForm $model
 */

$this->title = 'Новый вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $quizModel->source]],
    ['label' => $quizModel->title, 'url' => ['test/update', 'id' => $quizModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'isCreate' => true,
    'quizModel' => $quizModel,
    'renderData' => $this->render('_question', ['model' => $model, 'isNewRecord' => true]),
]); ?>
