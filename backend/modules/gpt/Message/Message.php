<?php

declare(strict_types=1);

namespace backend\modules\gpt\Message;

class Message implements \JsonSerializable
{
    /**
     * @var string
     */
    private $role;
    /**
     * @var string
     */
    private $content;

    public function __construct(string $role, string $content)
    {
        $this->role = $role;
        $this->content = $content;
    }

    public function jsonSerialize(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->content,
        ];
    }
}
