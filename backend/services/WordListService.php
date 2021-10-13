<?php

namespace backend\services;

use backend\components\import\WordListModifierBuilder;
use backend\models\question\QuestionType;
use backend\models\test\CreateStoryForm;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use Exception;

class WordListService
{

    private $storyService;
    private $storyTestService;
    private $transactionManager;
    private $storyEditorService;
    private $storySlideService;
    private $storyLinksService;

    public function __construct(StoryService $storyService,
                                StorySlideService $storySlideService,
                                StoryTestService $storyTestService,
                                TransactionManager $transactionManager,
                                StoryEditorService $storyEditorService,
                                StoryLinksService $storyLinksService)
    {
        $this->storyService = $storyService;
        $this->storySlideService = $storySlideService;
        $this->storyTestService = $storyTestService;
        $this->transactionManager = $transactionManager;
        $this->storyEditorService = $storyEditorService;
        $this->storyLinksService = $storyLinksService;
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

    private function processItems(int $storyId, array $items, array $words): void
    {
        foreach ($items as $item) {

            $test = $this->storyTestService->createFromTemplate($item->template_id);
            if (!$test->save()) {
                throw new Exception('Can\'t be saved StoryTest model. Errors: '. implode(', ', $test->getFirstErrors()));
            }

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

    public function createFromTemplate(int $userId, string $storyName, array $items, array $words): void
    {

        $this->transactionManager->wrap(function() use ($userId, $storyName, $items, $words) {

            $story = $this->storyService->create($storyName, $userId, [36]);
            if (!$story->save()) {
                throw new Exception('Can\'t be saved Story model. Errors: '. implode(', ', $story->getFirstErrors()));
            }

            foreach ($items as $item) {

                $test = $this->storyTestService->createFromTemplate($item->template_id);
                if (!$test->save()) {
                    throw new Exception('Can\'t be saved StoryTest model. Errors: '. implode(', ', $test->getFirstErrors()));
                }

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
                $slide = $this->storySlideService->create($story->id, $data, StorySlide::KIND_QUESTION);
                if (!$slide->save()) {
                    throw new Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $slide->getFirstErrors()));
                }
                $this->storyLinksService->createTestLink($story->id, $test->id);
            }

            $finalSlide = $this->storyEditorService->createFinalSlide($story->id);
            if (!$finalSlide->save()) {
                throw new Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $finalSlide->getFirstErrors()));
            }
        });
    }

    public function createFromTemplateExistsStory(int $storyId, array $items, array $words): void
    {
        $this->transactionManager->wrap(function() use ($storyId, $items, $words) {

            $story = Story::findModel($storyId);
            $this->storyEditorService->deleteFinalSlide($story->id);

            $this->processItems($story->id, $items, $words);

            $finalSlide = $this->storyEditorService->createFinalSlide($story->id);
            if (!$finalSlide->save()) {
                throw new Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $finalSlide->getFirstErrors()));
            }
        });
    }
}
