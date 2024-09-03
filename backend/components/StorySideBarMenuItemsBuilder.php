<?php

declare(strict_types=1);

namespace backend\components;

use common\models\Story;

class StorySideBarMenuItemsBuilder
{
    /**
     * @var Story
     */
    private $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }

    public function build(): array
    {
        $id = $this->story->id;
        return [
            'sidebarMenuItems' => [
                ['label' => $this->story->title, 'url' => ['/story/update', 'id' => $id]],
                ['label' => 'Редактор', 'url' => ['/editor/edit', 'id' => $id]],
                ['label' => 'Тесты', 'url' => ['/story-test/index', 'id' => $id]],
                ['label' => 'Разделы', 'url' => ['/course/update', 'id' => $id]],
                ['label' => 'Статистика', 'url' => ['/statistics/list', 'id' => $id]],
                ['label' => 'Озвучка', 'url' => ['/audio/index', 'story_id' => $id]],
                ['label' => 'Ментальные карты', 'url' => ['/mental-map-history/index', 'story_id' => $id]],
            ],
        ];
    }
}
