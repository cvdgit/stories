<?php

namespace backend\services;

use backend\components\import\AnswerDto;
use backend\components\import\DefaultWordProcessor;
use backend\components\import\QuestionDto;
use backend\components\import\SequenceWordProcessor;
use backend\components\import\WordListAdapter;
use backend\models\answer\DefaultAnswerModel;
use backend\models\answer\SequenceAnswerModel;
use backend\models\question\CreateQuestion;
use backend\models\question\sequence\CreateSequenceQuestion;
use backend\models\question\sequence\SortView;
use backend\models\test\import\ImportFromWordList;
use common\models\StoryTest;
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

    public function importFromWordList(StoryTest $quizModel, ImportFromWordList $form): void
    {
        if (!$form->validate()) {
            throw new DomainException(implode(PHP_EOL, $form->getErrorSummary(true)));
        }

        /** @var TestWordList $wordList */
        $wordList = TestWordList::find()
            ->where(['id' => $form->word_list_id])
            ->one();

        if ($form->isTypeSequence()) {
            $wordProcessor = new SequenceWordProcessor();
        }
        else {
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
        }

        $wordListAdapter = new WordListAdapter($wordList->testWords, $wordProcessor);
        $questions = $wordListAdapter->process();

        if ($form->isTypeSequence()) {
            $this->createSequenceQuestions($quizModel->id, $questions);
        }
        else {
            $this->createDefaultQuestions($quizModel->id, $questions);
        }
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
}
