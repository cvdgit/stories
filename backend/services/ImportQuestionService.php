<?php

namespace backend\services;

use backend\components\import\WordListAdapter;
use backend\models\answer\DefaultAnswerModel;
use backend\models\answer\SequenceAnswerModel;
use backend\models\question\CreateQuestion;
use backend\models\question\sequence\CreateSequenceQuestion;
use backend\models\question\sequence\SortView;
use backend\models\test\import\ImportFromWordList;
use common\models\StoryTest;
use common\models\TestWordList;
use common\services\TransactionManager;
use DomainException;

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

        $wordList = TestWordList::find()
            ->where(['id' => $form->word_list_id])
            ->one();

        $wordListAdapter = new WordListAdapter($wordList);

        if ($form->isTypeSequence()) {
            $questions = $wordListAdapter->createSequence($form->question_type);
            $this->createSequenceQuestions($quizModel->id, $questions);
        }
        else {
            $questions = $wordListAdapter->create($form->number_answers);
            $this->createDefaultQuestions($quizModel->id, $questions);
        }
    }

    private function createDefaultQuestions(int $quizId, array $questions): void
    {
        if (count($questions) === 0) {
            throw new DomainException('Список вопросов пуст');
        }
        foreach ($questions as $questionOrder => $question) {

            $questionForm = new CreateQuestion($quizId);
            $questionForm->name = $question['name'];
            $questionForm->order = $questionOrder;
            $questionModel = $this->questionService->createQuestion($questionForm);

            $questionAnswers = [];
            foreach ($question['answers'] as $answer) {

                $answerModel = new DefaultAnswerModel();
                $answerModel->name = $answer['name'];
                $answerModel->correct = $answer['correct'] ? 1 : 0;
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

                $questionForm = new CreateSequenceQuestion($quizId);
                $questionForm->order = $questionOrder;
                $questionForm->sort_view = SortView::HORIZONTAL;
                $questionModel = $this->questionService->createSequenceQuestion($questionForm);

                $questionAnswers = [];
                foreach ($question['answers'] as $answerOrder => $answer) {

                    $answerModel = new SequenceAnswerModel();
                    $answerModel->name = $answer['name'];
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
