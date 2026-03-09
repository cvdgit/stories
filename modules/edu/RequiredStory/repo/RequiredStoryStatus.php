<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use DomainException;

class RequiredStoryStatus
{
    private const OPEN = 'open';
    private const CLOSE = 'close';
    /**
     * @var string
     */
    private $status;

    private static $labels = [
        self::OPEN => 'Действующая',
        self::CLOSE => 'Закрытая',
    ];

    public function __construct(string $status)
    {
        $all = [self::OPEN, self::CLOSE];
        if (!in_array($status, $all)) {
            throw new DomainException('Unknown required story status');
        }
        $this->status = $status;
    }

    public static function open(): self
    {
        return new self(self::OPEN);
    }

    public static function close(): self
    {
        return new self(self::CLOSE);
    }

    public function __toString()
    {
        return $this->status;
    }

    public function label(): string
    {
        return self::$labels[$this->status];
    }

    public static function all(): array
    {
        return array_keys(self::$labels);
    }

    public static function values(): array
    {
        return self::$labels;
    }
}
