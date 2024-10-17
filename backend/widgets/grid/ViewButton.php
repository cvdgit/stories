<?php

declare(strict_types=1);

namespace backend\widgets\grid;

class ViewButton extends Button
{
    public function __invoke(array $options = []): string
    {
        return $this->createButton('eye-open', 'Просмотр', $options);
    }
}
