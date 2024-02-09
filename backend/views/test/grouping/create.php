<?php

declare(strict_types=1);

use backend\Testing\Questions\Grouping\Create\CreateGroupingForm;
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $quizModel
 * @var CreateGroupingForm $formModel
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
    'renderData' => $this->render('_question', [
        'formModel' => $formModel,
        'isNewRecord' => true,
    ]),
]); ?>
