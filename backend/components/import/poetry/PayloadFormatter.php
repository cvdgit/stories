<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

use Ramsey\Uuid\Uuid;

class PayloadFormatter
{
    /**
     * @return array{"id": string, "title": string, "correct": bool}
     */
    public function createFragment(string $id, string $title, bool $correct): array
    {
        return [
            'id' => $id,
            'title' => $title,
            'correct' => $correct,
        ];
    }

    public function createUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @param string $content
     * @param array $fragments
     * @return array{"content": string, "fragments": array{"id": string, "title": string, "correct": bool}}
     */
    public function format(string $content, array $fragments): array
    {
        return [
            'content' => '<div>' . $content . '</div>',
            'fragments' => $fragments,
        ];
    }
}
