<?php

declare(strict_types=1);

namespace backend\services;

use backend\forms\FragmentListForm;
use backend\forms\FragmentListItemForm;
use common\services\TransactionManager;
use Exception;
use LogicException;
use Yii;
use yii\db\Connection;
use yii\db\Query;

class QuestionFragmentService
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param int $userId
     * @param FragmentListForm $form
     * @param list<FragmentListItemForm> $items
     * @param int|null $testingId
     * @throws Exception
     */
    public function create(int $userId, FragmentListForm $form, array $items, int $testingId = null): void
    {
        $this->transactionManager->wrap(function() use ($form, $userId, $items, $testingId) {

            $db = Yii::$app->db;

            $db->createCommand()
                ->insert('fragment_list', [
                    'name' => $form->name,
                    'created_at' => time(),
                    'created_by' => $userId,
                ])
                ->execute();

            $listId = (int)$db->lastInsertID;
            $rows = [];
            foreach ($items as $itemForm) {
                $name = trim($itemForm->name);
                if ($name === '') {
                    continue;
                }
                $rows[] = [
                    'name' => $itemForm->name,
                    'fragment_list_id' => $listId,
                ];
            }

            if (count($rows) === 0) {
                throw new LogicException('Невозможно создать. Список слов пуст');
            }

            $db->createCommand()
                ->batchInsert('fragment_list_item', ['name', 'fragment_list_id'], $rows)
                ->execute();

            $this->insertTags($listId, $this->prepareTagString($form->keywords));

            if ($testingId !== null) {
                $this->insertTestingLink($db, $listId, $testingId);
            }
        });
    }

    private function prepareTagString(string $tags): array
    {
        return array_unique(preg_split(
            '/\s*,\s*/u',
            preg_replace(
                '/\s+/u',
                ' ',
                $tags
            ),
            -1,
            PREG_SPLIT_NO_EMPTY
        ));
    }

    /**
     * @param int $listId
     * @param array<string> $tags
     * @return void
     */
    private function insertTags(int $listId, array $tags): void
    {
        $db = Yii::$app->db;

        $rows = [];
        foreach ($tags as $tagName) {

            $tagId = (int)(new Query())
                ->select('id')
                ->from('tag')
                ->where(['name' => $tagName])
                ->scalar();

            if (!$tagId) {
                $db->createCommand()
                    ->insert('tag', [
                        'name' => $tagName,
                        'frequency' => 1
                    ])
                    ->execute();
                $tagId = (int)$db->lastInsertID;
            }

            $rows[] = [$listId, $tagId];
        }

        if (!empty($rows)) {
            $db
                ->createCommand()
                ->batchInsert('fragment_list_tag', ['fragment_list_id', 'tag_id'], $rows)
                ->execute();
        }
    }

    private function insertTestingLink(Connection $db, int $listId, int $testingId): void
    {
        $db->createCommand()
            ->insert('fragment_list_testing', [
                'fragment_list_id' => $listId,
                'testing_id' => $testingId
            ])
            ->execute();
    }
}
