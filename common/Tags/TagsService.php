<?php

declare(strict_types=1);

namespace common\Tags;

use yii\db\Query;

class TagsService
{
    /** @var CreateTagsHandler */
    private $handler;

    public function __construct(CreateTagsHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param string $tags
     * @return array<array-key, int>
     */
    public function processTags(string $tags): array
    {
        $tagIds = [];
        foreach ($this->prepareTagString($tags) as $tagName) {
            $tagId = (int) (new Query())
                ->select('id')
                ->from('tag')
                ->where(['name' => $tagName])
                ->scalar();
            if (empty($tagId)) {
                $tagId = $this->handler->handle(new CreateTagsCommand($tagName));
            }
            $tagIds[] = $tagId;
        }
        return $tagIds;
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
}
