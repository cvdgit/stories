<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest\Fragments;

use common\services\TransactionManager;
use LogicException;
use Yii;
use yii\db\Query;

class SaveFragmentHandler
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(SaveFragmentCommand $command): void
    {
        $fragmentListId = (new Query())
            ->select('id')
            ->from('fragment_list')
            ->where(['name' => $command->getName()])
            ->scalar();

        if (empty($fragmentListId)) {
            $this->createFragmentList($command);
        } else {
            $this->updateFragmentList((int) $fragmentListId, $command);
        }
    }

    private function createFragmentList(SaveFragmentCommand $command): void
    {
        $this->transactionManager->wrap(function() use ($command) {

            $db = Yii::$app->db;

            $db->createCommand()
                ->insert('fragment_list', [
                    'name' => $command->getName(),
                    'created_at' => time(),
                    'created_by' => $command->getUserId(),
                ])
                ->execute();

            $listId = (int)$db->lastInsertID;

            $this->insertFragmentItems($listId, $command->getItems());
        });
    }

    private function updateFragmentList(int $fragmentListId, SaveFragmentCommand $command): void
    {
        $this->transactionManager->wrap(function() use ($fragmentListId, $command) {

            $db = Yii::$app->db;

            $db->createCommand()
                ->delete('fragment_list_item', [
                    'fragment_list_id' => $fragmentListId
                ])
                ->execute();

            $this->insertFragmentItems($fragmentListId, $command->getItems());
        });
    }

    /**
     * @param int $fragmentListId
     * @param array<array-key, array{name: string}> $items
     * @return void
     */
    private function insertFragmentItems(int $fragmentListId, array $items): void
    {
        $rows = [];
        foreach ($items as $item) {
            $name = trim($item['name']);
            if ($name === '') {
                continue;
            }
            $rows[] = [
                'name' => $name,
                'fragment_list_id' => $fragmentListId,
            ];
        }

        if (count($rows) === 0) {
            throw new LogicException('Невозможно создать. Список слов пуст');
        }

        Yii::$app->db->createCommand()
            ->batchInsert('fragment_list_item', ['name', 'fragment_list_id'], $rows)
            ->execute();
    }
}
