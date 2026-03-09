<?php

declare(strict_types=1);

namespace backend\widgets;

interface SelectUserItemInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getEmail(): string;

    public function getPhoto(): string;
}
