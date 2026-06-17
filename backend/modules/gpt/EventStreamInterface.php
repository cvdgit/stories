<?php

declare(strict_types=1);

namespace backend\modules\gpt;

interface EventStreamInterface
{
    public function send(string $target, string $url, string $fieldsJson): void;
}
