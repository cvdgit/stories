<?php

declare(strict_types=1);

use backend\Testing\Questions\Step\Create\StepQuestionCreateForm;
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $quizModel
 * @var StepQuestionCreateForm $formModel
 * @var string $steps
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
        'action' => Url::to(['/test/step/create-handler', 'test_id' => $quizModel->id]),
        'steps' => $steps,
    ]),
]) ?>
