<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdateTestsRepeat;

use common\models\StoryTest;
use common\services\TestHistoryService;
use common\services\TransactionManager;
use DomainException;
use Exception;

class UpdateTestsRepeatHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var TestHistoryService
     */
    private $historyService;

    public function __construct(TransactionManager $transactionManager, TestHistoryService $historyService)
    {
        $this->transactionManager = $transactionManager;
        $this->historyService = $historyService;
    }

    /**
     * @throws Exception
     */
    public function handle(UpdateTestsRepeatForm $command): void
    {
        $testModel = StoryTest::findOne($command->testId);
        if ($testModel === null) {
            throw new DomainException("Тест не найден");
        }
        $testModel->repeat = $command->repeat;

        $this->transactionManager->wrap(function() use ($testModel) {

            if (!$testModel->save()) {
                throw new DomainException("Ошибка при сохранении теста");
            }

            $this->historyService->clearTestHistory($testModel->id);
        });
    }
}
