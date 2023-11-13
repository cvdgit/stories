<?php

declare(strict_types=1);

namespace backend\services;

use backend\components\import\AnswerDto;
use backend\components\import\DefaultWordProcessor;
use backend\components\import\PoetryWordProcessor;
use backend\components\import\QuestionDto;
use backend\components\import\SequenceWordProcessor;
use backend\components\import\WordListAdapter;
use backend\components\import\WordProcessor;
use backend\models\answer\DefaultAnswerModel;
use backend\models\answer\SequenceAnswerModel;
use backend\models\question\CreateQuestion;
use backend\models\question\QuestionType;
use backend\models\question\sequence\CreateSequenceQuestion;
use backend\models\question\sequence\SortView;
use backend\models\test\import\ImportFromWordList;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\models\TestWord;
use common\models\TestWordList;
use common\services\TransactionManager;
use DomainException;
use yii\db\Query;

class ImportQuestionService
{
    private $transactionManager;
    private $questionService;
    private $answerService;

    public function __construct(TransactionManager $transactionManager, QuestionService $questionService, AnswerService $answerService)
    {
        $this->transactionManager = $transactionManager;
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

    public function processDefault(int $testingId, ImportFromWordList $form): void
    {
        $wordList = TestWordList::findOne($form->word_list_id);
        if ($wordList === null) {
            throw new DomainException('Список слов не найден');
        }

        $wordIds = (new Query())
            ->select(['word_id' => 'MIN(id)'])
            ->from('test_word')
            ->where(['word_list_id' => $wordList->id])
            ->groupBy(['correct_answer'])
            ->indexBy('word_id')
            ->all();
        $wordIds = array_keys($wordIds);
        $words = TestWord::find()->where(['in', 'id', $wordIds])->all();

        $wordProcessor = new DefaultWordProcessor($words, $form->number_answers);
        $questions = $this->processWordList($wordList->testWords, $wordProcessor);
        $this->createDefaultQuestions($testingId, $questions);
    }

    public function processSequence(int $testingId, ImportFromWordList $form): void
    {
        $wordList = TestWordList::findOne($form->word_list_id);
        if ($wordList === null) {
            throw new DomainException('Список слов не найден');
        }
        $wordProcessor = new SequenceWordProcessor();
        $questions = $this->processWordList($wordList->testWords, $wordProcessor);
        $this->createSequenceQuestions($testingId, $questions);
    }

    public function processPoetry(int $testingId, ImportFromWordList $form): void
    {
        $wordList = TestWordList::findOne($form->word_list_id);
        if ($wordList === null) {
            throw new DomainException('Список слов не найден');
        }

        $words = $wordList->getTestWords()
            ->indexBy('order')
            ->all();
        $wordProcessor = new PoetryWordProcessor($words);
        $questions = $this->processWordList($wordList->testWords, $wordProcessor);
        $this->createPoetryQuestions($testingId, $questions);
    }

    private function processWordList(array $words, WordProcessor $processor): array
    {
        return (new WordListAdapter($words, $processor))->process();
    }

    private function createDefaultQuestions(int $quizId, array $questions): void
    {
        if (count($questions) === 0) {
            throw new DomainException('Список вопросов пуст');
        }
        foreach ($questions as $questionOrder => $question) {
            /** @var QuestionDto $question */

            $questionForm = new CreateQuestion($quizId);
            $questionForm->name = $question->getName();
            $questionForm->order = $questionOrder;
            $questionModel = $this->questionService->createQuestion($questionForm);

            $questionAnswers = [];
            foreach ($question->getAnswers() as $answer) {
                /** @var AnswerDto $answer */
                $answerModel = new DefaultAnswerModel();
                $answerModel->name = $answer->getName();
                $answerModel->correct = $answer->isCorrect() ? 1 : 0;
                $questionAnswers[] = $this->answerService->createAnswer($answerModel);
            }
            $questionModel->storyTestAnswers = $questionAnswers;

            if (!$questionModel->save()) {
                throw new DomainException('Question save exception');
            }
        }
    }

    private function createSequenceQuestions(int $quizId, array $questions): void
    {
        if (count($questions) === 0) {
            throw new DomainException('Список вопросов пуст');
        }
        $this->transactionManager->wrap(function() use ($quizId, $questions) {

            foreach ($questions as $questionOrder => $question) {
                /** @var QuestionDto $question */
                $questionForm = new CreateSequenceQuestion($quizId);
                $questionForm->order = $questionOrder;
                $questionForm->sort_view = SortView::HORIZONTAL;
                $questionModel = $this->questionService->createSequenceQuestion($questionForm);

                $questionAnswers = [];
                foreach ($question->getAnswers() as $answerOrder => $answer) {
                    /** @var AnswerDto $answer */
                    $answerModel = new SequenceAnswerModel();
                    $answerModel->name = $answer->getName();
                    $answerModel->order = $answerOrder;
                    $questionAnswers[] = $this->answerService->createSequenceAnswer($answerModel);
                }
                $questionModel->storyTestAnswers = $questionAnswers;

                if (!$questionModel->save()) {
                    throw new DomainException(implode(PHP_EOL, $questionModel->getErrorSummary(true)));
                }
            }
        });
    }

    private function createPoetryQuestions(int $quizId, array $questions): void
    {
        if (count($questions) === 0) {
            throw new DomainException('Список вопросов пуст');
        }
        $this->transactionManager->wrap(function() use ($quizId, $questions) {
            foreach ($questions as $questionOrder => $questionDto) {
                /** @var QuestionDto $questionDto */

                if ($questionDto->getAnswersCount() === 0) {
                    continue;
                }

                $question = StoryTestQuestion::createPoetry($quizId, $questionDto->getName(), $questionOrder);
                $answers = [];
                foreach ($questionDto->getAnswers() as $answerDto) {
                    /** @var AnswerDto $answerDto */
                    $answers[] = StoryTestAnswer::createFromRelation($answerDto->getName(), $answerDto->isCorrect(), $answerDto->getDescription());
                }
                $question->storyTestAnswers = $answers;
                if (!$question->save()) {
                    throw new DomainException(implode(PHP_EOL, $question->getErrorSummary(true)));
                }
            }
        });
    }

    public function createFromJson(int $testId, array $questions): void
    {
        $testModel = StoryTest::findOne($testId);
        if ($testModel === null) {
            throw new DomainException("Тест не найден");
        }
        if (count($questions) === 0) {
            throw new DomainException('Список вопросов пуст');
        }
        foreach ($questions as $question) {

            $questionForm = new CreateQuestion($testId);
            $questionForm->name = $question->question;

            $questionForm->type = QuestionType::ONE;
            if (count(array_filter($question->answers, static function($answer) {
                return $answer->correct;
                })) > 1) {
                $questionForm->type = QuestionType::MANY;
            }

            $questionModel = $this->questionService->createQuestion($questionForm);

            $questionAnswers = [];
            foreach ($question->answers as $answer) {
                $answerModel = new DefaultAnswerModel();
                $answerModel->name = $answer->answer;
                $answerModel->correct = $answer->correct ? 1 : 0;
                $questionAnswers[] = $this->answerService->createAnswer($answerModel);
            }
            $questionModel->storyTestAnswers = $questionAnswers;

            if (!$questionModel->save()) {
                throw new DomainException('Question save exception');
            }
        }
    }
}
