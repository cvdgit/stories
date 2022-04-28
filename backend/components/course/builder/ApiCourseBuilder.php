<?php

namespace backend\components\course\builder;

class ApiCourseBuilder
{

    private function createLesson(string $uuid, string $title, string $type): array
    {
        return [
            'id' => $uuid,
            'title' => $title,
            'type' => $type,
            'items' => [],
        ];
    }

    public function createBlocksLesson(string $uuid, string $title): array
    {
        return $this->createLesson($uuid, $title, 'blocks');
    }

    public function createQuizLesson(string $uuid, string $title, string $description): array
    {
        return array_merge($this->createLesson($uuid, $title, 'quiz'), [
            'description' => $description,
        ]);
    }

    public function addBlock(&$lesson, array $block): void
    {
        $lesson['items'][] = $block;
    }

    public function addDivider(&$lesson, int $id): void
    {
        $this->addBlock($lesson, [
            'id' => $id,
            'type' => 'divider',
            'items' => [],
        ]);
    }

    public function addQuizBlock(&$lesson, int $id, array $items): void
    {
        $this->addBlock($lesson, [
            'id' => $id,
            'type' => 'quiz',
            'items' => $items,
            'settings' => [
                'passToContinue' => false,
            ],
        ]);
    }
}
