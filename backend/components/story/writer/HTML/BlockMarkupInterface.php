<?php

declare(strict_types=1);

namespace backend\components\story\writer\HTML;

interface BlockMarkupInterface {
    public function markup(): string;
}
