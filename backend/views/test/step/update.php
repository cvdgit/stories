<?php

declare(strict_types=1);

use backend\Testing\Questions\Step\Update\StepQuestionUpdateForm;
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $quizModel
 * @var StepQuestionUpdateForm $formModel
 * @var StoryTestQuestion $questionModel
 * @var string $steps
 */

$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $quizModel->source]],
    ['label' => $quizModel->title, 'url' => ['test/update', 'id' => $quizModel->id]],
    $this->title,
];
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $quizModel,
    'currentModelId' => $questionModel->id,
    'renderData' => $this->render('_question', [
        'formModel' => $formModel,
        'isNewRecord' => false,
        'testingId' => $quizModel->id,
        'action' => Url::to(['/test/step/update-handler', 'id' => $questionModel->id]),
        'steps' => $steps,
    ]),
]) ?>
