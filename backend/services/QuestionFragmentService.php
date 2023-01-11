<?php

declare(strict_types=1);

namespace backend\services;

use backend\forms\FragmentListForm;
use backend\forms\FragmentListItemForm;
use common\services\TransactionManager;
use Exception;
use Yii;
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
     * @throws Exception
     */
    public function create(int $userId, FragmentListForm $form, array $items): void
    {
        $this->transactionManager->wrap(function() use ($form, $userId, $items) {

            $db = Yii::$app->db;

            $db->createCommand()
                ->insert('fragment_list', [
                    'name' => $form->name,
                    'created_at' => time(),
                    'created_by' => $userId,
                ])
                ->execute();

            $listId = (int)$db->lastInsertID;
            $rows = array_map(static function($itemForm) use ($listId) {
                return [
                    'name' => $itemForm->name,
                    'fragment_list_id' => $listId,
                ];
            }, $items);

            if (count($rows)) {
                $db->createCommand()
                    ->batchInsert('fragment_list_item', ['name', 'fragment_list_id'], $rows)
                    ->execute();
            }

            $this->insertTags($listId, $this->prepareTagString($form->keywords));
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
}
