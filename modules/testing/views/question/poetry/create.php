<?php

declare(strict_types=1);

use modules\testing\forms\PoetryForm;
use modules\testing\models\Question;
use modules\testing\models\Testing;
use modules\testing\widgets\QuestionViewWidget;

/**
 * @var Testing $testing
 * @var PoetryForm $formModel
 */

$this->title = 'Новый вопрос';

$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['/test/index', 'source' => $testing->source]],
    ['label' => $testing->title, 'url' => ['test/update', 'id' => $testing->id]],
    $this->title,
];
?>
<?= QuestionViewWidget::widget([
    'isCreate' => true,
    'renderData' => $this->render('_question', ['model' => $formModel, 'isNewRecord' => true]),
    'questionItems' => $testing->questions,
    'questionItemCallback' => static function(Question $item, int $currentQuestionId = null) {
        return [
            'label' => $item->name,
            'url' => ['test/update-question', 'question_id' => $item->id],
            'active' => $item->id === $currentQuestionId,
        ];
    }
]) ?>
