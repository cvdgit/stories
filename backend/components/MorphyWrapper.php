<?php

declare(strict_types=1);

namespace backend\components;

use cijic\phpMorphy\Morphy;

class MorphyWrapper
{
    private $morphy;

    public function __construct()
    {
        $this->morphy = new Morphy();
    }

    public function getPseudoRoot(string $word): ?string
    {
        $result = $this->morphy->getPseudoRoot(mb_strtoupper($word));
        if ($result === false) {
            return null;
        }
        return $result[0];
    }

    public function getBaseForm(string $word): ?string
    {
        $result = $this->morphy->getBaseForm(mb_strtoupper($word));
        if ($result === false) {
            return null;
        }
        return $result[0];
    }

    public function getAllForms(string $word): array
    {
        $result = $this->morphy->getAllForms(mb_strtoupper($word));
        if ($result === false) {
            return [];
        }
        return $result;
    }
}
