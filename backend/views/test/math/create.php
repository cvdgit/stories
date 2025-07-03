<?php

declare(strict_types=1);

use backend\Testing\Questions\Math\Create\MathQuestionCreateForm;
use backend\widgets\QuestionManageWidget;
use common\models\StoryTest;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $quizModel
 * @var MathQuestionCreateForm $formModel
 * @var string $answers
 * @var string $fragments
 * @var bool $isGapsQuestion
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
    'renderData' => $this->render($isGapsQuestion ? '_question_gaps' : '_question', [
        'formModel' => $formModel,
        'isNewRecord' => true,
        'action' => Url::to(['/test/math/create-handler', 'test_id' => $quizModel->id, 'gaps' => $isGapsQuestion ? '1': null]),
        'answers' => $answers,
        'fragments' => $fragments,
    ]),
]) ?>
