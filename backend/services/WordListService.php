<?php


namespace backend\services;


use backend\models\test\CreateStoryForm;
use common\models\StorySlide;
use common\services\TransactionManager;

class WordListService
{

    private $storyService;
    private $storyTestService;
    private $transactionManager;
    private $storyEditorService;
    private $storySlideService;

    public function __construct(StoryService $storyService,
                                StorySlideService $storySlideService,
                                StoryTestService $storyTestService,
                                TransactionManager $transactionManager,
                                StoryEditorService $storyEditorService)
    {
        $this->storyService = $storyService;
        $this->storySlideService = $storySlideService;
        $this->storyTestService = $storyTestService;
        $this->transactionManager = $transactionManager;
        $this->storyEditorService = $storyEditorService;
    }

    public function create(CreateStoryForm $form, int $userID)
    {
        $this->transactionManager->wrap(function() use ($form, $userID) {

            $test = $this->storyTestService->createFromWordList($form->test_name, $form->word_list_id, $form->test_answer_type);
            if (!$test->save()) {
                throw new \Exception('Can\'t be saved StoryTest model. Errors: '. implode(', ', $test->getFirstErrors()));
            }

            $story = $this->storyService->create($form->story_name, $userID, [36]);
            if (!$story->save()) {
                throw new \Exception('Can\'t be saved Story model. Errors: '. implode(', ', $story->getFirstErrors()));
            }

            $data = $this->storyEditorService->createQuestionBlock(['test-id' => $test->id]);
            $slide = $this->storySlideService->create($story->id, $data, StorySlide::KIND_QUESTION);
            $slide->save();
        });
    }

}