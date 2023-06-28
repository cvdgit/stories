<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Update;

class UpdateFileCommand
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $captions;

    public function __construct(int $id, string $name, string $captions = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->captions = $captions;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getCaptions(): ?string
    {
        return $this->captions;
    }
}
