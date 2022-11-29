<?php

declare(strict_types=1);

namespace backend\services;

use backend\components\import\AnswerDto;
use backend\components\import\poetry\EndLinesPayload;
use backend\components\import\poetry\BeginLinesPayload;
use backend\components\import\poetry\EvenLinesPayload;
use backend\components\import\poetry\OddLinesPayload;
use backend\components\import\poetry\PoetryDragWordsQuestionBuilder;
use backend\components\import\poetry\WordFormatter;
use backend\components\import\PoetryWordProcessor;
use backend\components\import\QuestionDto;
use backend\components\import\WordListAdapter;
use backend\components\import\WordListModifierBuilder;
use backend\components\import\WordProcessor;
use backend\forms\WordListForm;
use backend\forms\WordListPoetryForm;
use backend\models\question\QuestionType;
use backend\models\test\CreateStoryForm;
use common\components\ModelDomainException;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\models\TestWord;
use common\models\TestWordList;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Yii;
use yii\di\Instance;

class WordListService
{
    private $storyService;
    private $storyTestService;
    private $transactionManager;
    private $storyEditorService;
    private $storySlideService;
    private $storyLinksService;
    private $dragWordsService;

    public function __construct(
        StoryService $storyService,
        StorySlideService $storySlideService,
        StoryTestService $storyTestService,
        TransactionManager $transactionManager,
        StoryEditorService $storyEditorService,
        StoryLinksService $storyLinksService,
        DragWordsService $dragWordsService
    ) {
        $this->storyService = $storyService;
        $this->storySlideService = $storySlideService;
        $this->storyTestService = $storyTestService;
        $this->transactionManager = $transactionManager;
        $this->storyEditorService = $storyEditorService;
        $this->storyLinksService = $storyLinksService;
        $this->dragWordsService = $dragWordsService;
    }

    public function createWordList(WordListForm $form): void
    {
        $wordList = TestWordList::create($form->name, $form->story_id === '' ? null : (int)$form->story_id);
        if (!$wordList->save()) {
            throw ModelDomainException::create($wordList);
        }
    }

    public function updateWordList(TestWordList $wordList, WordListForm $form): void
    {
        $wordList->updateWordList($form->name, $form->story_id === '' ? null : (int)$form->story_id);
        if (!$wordList->save()) {
            throw ModelDomainException::create($wordList);
        }
    }

    public function create(CreateStoryForm $form, int $userID)
    {
        $this->transactionManager->wrap(function() use ($form, $userID) {

            $test = $this->storyTestService->createFromWordList($form->test_name, $form->word_list_id, $form->test_answer_type, $form->test_shuffle_word_list, $form->test_strict_answer);
            if (!$test->save()) {
                throw new Exception('Can\'t be saved StoryTest model. Errors: '. implode(', ', $test->getFirstErrors()));
            }

            $story = $this->storyService->create($form->story_name, $userID, [36]);
            if (!$story->save()) {
                throw new Exception('Can\'t be saved Story model. Errors: '. implode(', ', $story->getFirstErrors()));
            }

            $data = $this->storyEditorService->createQuestionBlock(['test-id' => $test->id]);
            $slide = $this->storySlideService->create($story->id, $data, StorySlide::KIND_QUESTION);
            $slide->save();

            $this->storyLinksService->createTestLink($story->id, $test->id);
        });
    }

    private function processItems(int $storyId, array $items, TestWordList $wordList): void
    {
        foreach ($items as $item) {

            $test = $this->storyTestService->createFromTemplate($item->template_id, $wordList->name);
            if (!$test->save()) {
                throw new Exception('Can\'t be saved StoryTest model. Errors: '. implode(', ', $test->getFirstErrors()));
            }

            $words = $wordList->testWords;
            $modifier = WordListModifierBuilder::build($item->word_list_processing, $words);
            $questionsData = $modifier->modify();

            foreach ($questionsData as $question) {
                $questionModel = StoryTestQuestion::create($test->id, $question->getName(), QuestionType::ONE);
                $questionAnswers = [];
                foreach ($question->getAnswers() as $answer) {
                    $questionAnswers[] = StoryTestAnswer::createFromRelation($answer->getName(), $answer->isCorrect(), $answer->getDescription());
                }
                $questionModel->storyTestAnswers = $questionAnswers;
                if (!$questionModel->save()) {
                    throw new Exception('Can\'t be saved StoryTestQuestion model. Errors: '. implode(', ', $questionModel->getFirstErrors()));
                }
            }

            $data = $this->storyEditorService->createQuestionBlock(['test-id' => $test->id]);
            $slide = $this->storySlideService->create($storyId, $data, StorySlide::KIND_QUESTION);
            if (!$slide->save()) {
                throw new Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $slide->getFirstErrors()));
            }
            $this->storyLinksService->createTestLink($storyId, $test->id);
        }
    }

    public function createFromTemplate(int $userId, string $storyName, array $items, TestWordList $wordList): void
    {

        $this->transactionManager->wrap(function() use ($userId, $storyName, $items, $wordList) {

            $story = $this->storyService->create($storyName, $userId, [36]);
            if (!$story->save()) {
                throw new Exception('Can\'t be saved Story model. Errors: '. implode(', ', $story->getFirstErrors()));
            }

            $this->processItems($story->id, $items, $wordList);

            $finalSlide = $this->storyEditorService->createFinalSlide($story->id);
            if (!$finalSlide->save()) {
                throw new Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $finalSlide->getFirstErrors()));
            }
        });
    }

    public function createFromTemplateExistsStory(int $storyId, array $items, TestWordList $wordList): void
    {

        $this->transactionManager->wrap(function() use ($storyId, $items, $wordList) {

            $story = Story::findModel($storyId);
            $this->storyEditorService->deleteFinalSlide($story->id);

            $this->processItems($story->id, $items, $wordList);

            $finalSlide = $this->storyEditorService->createFinalSlide($story->id);
            if (!$finalSlide->save()) {
                throw new Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $finalSlide->getFirstErrors()));
            }
        });
    }

    /**
     * @param WordListPoetryForm $form
     * @param iterable<TestWord> $words
     */
    public function createPoetry(int $userId, WordListPoetryForm $form, array $words): void
    {
        $story = $this->storyService->create($form->name, $userId, [36]);
        if (!$story->save()) {
            throw ModelDomainException::create($story);
        }

        $builders = [
            Yii::createObject(PoetryDragWordsQuestionBuilder::class, [
                'Четные строки',
                Instance::of(EvenLinesPayload::class),
            ]),
            Yii::createObject(PoetryDragWordsQuestionBuilder::class, [
                'Нечетные строки',
                Instance::of(OddLinesPayload::class),
            ]),
            Yii::createObject(PoetryDragWordsQuestionBuilder::class, [
                'Выбор окончания строки',
                Instance::of(EndLinesPayload::class),
            ]),
            Yii::createObject(PoetryDragWordsQuestionBuilder::class, [
                'Выбор начала строки',
                Instance::of(BeginLinesPayload::class),
            ]),
        ];

        $formattedWords = (new WordFormatter())->formatWords($words, (int)$form->line_per_question);

        $this->transactionManager->wrap(function() use ($builders, $formattedWords, $form, $story, $words) {

            foreach ($builders as $builder) {

                $questions = $builder->createQuestions($formattedWords);

                $testing = StoryTest::createPoetry($form->name . ' - ' . $builder->getTitle());
                if (!$testing->save()) {
                    throw ModelDomainException::create($testing);
                }

                foreach ($questions as $questionDto) {
                    $question = $this->createPoetryQuestion($testing->id, 'Расставьте слова по своим местам', $questionDto->getPayload());
                    if (!$question->save()) {
                        throw ModelDomainException::create($question);
                    }
                    $this->dragWordsService->createAnswers($question->id, $questionDto->getPayload());
                }

                $data = $this->storyEditorService->createQuestionBlock(['test-id' => $testing->id]);
                $slide = $this->storySlideService->create($story->id, $data, StorySlide::KIND_QUESTION);
                if (!$slide->save()) {
                    throw ModelDomainException::create($slide);
                }

                $this->storyLinksService->createTestLink($story->id, $testing->id);
            }

            $testing = StoryTest::createPoetry($form->name . ' - Запоминание стихов');
            if (!$testing->save()) {
                throw ModelDomainException::create($testing);
            }

            $wordProcessor = new PoetryWordProcessor($words);
            $questions = $this->processWordList($words, $wordProcessor);
            $this->createPoetryQuestions($testing->id, $questions);

            $data = $this->storyEditorService->createQuestionBlock(['test-id' => $testing->id]);
            $slide = $this->storySlideService->create($story->id, $data, StorySlide::KIND_QUESTION);
            if (!$slide->save()) {
                throw ModelDomainException::create($slide);
            }

            $this->storyLinksService->createTestLink($story->id, $testing->id);

            $finalSlide = $this->storyEditorService->createFinalSlide($story->id);
            if (!$finalSlide->save()) {
                throw ModelDomainException::create($finalSlide);
            }
        });
    }

    private function processWordList(array $words, WordProcessor $processor): array
    {
        return (new WordListAdapter($words, $processor))->process();
    }

    private function createPoetryQuestion(int $quizId, string $name, string $payload): StoryTestQuestion
    {
        $question = StoryTestQuestion::create($quizId, $name, QuestionType::DRAG_WORDS);
        $question->regions = $payload;
        return $question;
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
                    $answers[] = StoryTestAnswer::createFromRelation($answerDto->getName(), (int)$answerDto->isCorrect(), $answerDto->getDescription());
                }
                $question->storyTestAnswers = $answers;
                if (!$question->save()) {
                    throw new DomainException(implode(PHP_EOL, $question->getErrorSummary(true)));
                }
            }
        });
    }
}
