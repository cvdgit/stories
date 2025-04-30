<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

class UserItem implements \JsonSerializable
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
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $photo;

    public function __construct(int $id, string $name, string $email, string $photo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->photo = $photo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'photo' => $this->getPhoto(),
        ];
    }
}
