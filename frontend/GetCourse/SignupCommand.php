<?php

declare(strict_types=1);

namespace frontend\GetCourse;

class SignupCommand
{
    /**
     * @var int
     */
    private $getCourseId;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;

    public function __construct(int $getCourseId, string $email, string $firstName, string $lastName)
    {
        $this->getCourseId = $getCourseId;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getGetCourseId(): int
    {
        return $this->getCourseId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
