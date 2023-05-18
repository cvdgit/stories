<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\Import;

use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;

class ImportHandler
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(ImportCommand $command): void
    {
        $test = StoryTest::findOne($command->getToTestId());
        if ($test === null) {
            throw new \DomainException('Тест не найден');
        }

        $nextQuestionOrder = $test->getMaxQuestionsOrder() + 1;

        $this->transactionManager->wrap(static function() use ($command, $nextQuestionOrder) {

            foreach ($command->getQuestionIds() as $questionId) {

                $question = StoryTestQuestion::findOne(['id' => $questionId, 'story_test_id' => $command->getFromTestId()]);
                if ($question === null) {
                    throw new \DomainException('Вопрос не найден');
                }

                $newQuestion = StoryTestQuestion::createFromQuestion($question, $command->getToTestId(), $nextQuestionOrder);
                if (!$newQuestion->save()) {
                    throw new \DomainException('Ошибка при создании вопроса');
                }

                foreach ($question->storyTestAnswers as $answer) {
                    $newAnswer = StoryTestAnswer::createFromAnswer($answer, $newQuestion->id);
                    if (!$newAnswer->save()) {
                        throw new \DomainException('Ошибка при создании ответа');
                    }
                }

                $nextQuestionOrder++;
            }
        });
    }
}
