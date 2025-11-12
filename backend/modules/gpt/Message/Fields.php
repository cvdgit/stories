<?php

declare(strict_types=1);

namespace backend\modules\gpt\Message;

use Ramsey\Uuid\Uuid;

class Fields implements \JsonSerializable
{
    /** @var Message[] */
    private $messages;

    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    public function addMessage(Message $message): void
    {
        $this->messages[] = $message;
    }

    public function jsonSerialize(): array
    {
        return [
            "input" => [
                "messages" => $this->messages,
            ],
            "config" => [
                "metadata" => [
                    "conversation_id" => Uuid::uuid4()->toString(),
                ],
            ],
            "include_names" => [],
        ];
    }
}
