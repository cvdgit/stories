<?php

declare(strict_types=1);

namespace backend\components\story\reader\blocks;

use backend\components\story\AbstractBlock;

class AbstractBlockReader
{
    /** @var \phpQueryObject */
    protected $pqBlock;

    public function __construct(\phpQueryObject $bqBlock)
    {
        $this->pqBlock = $bqBlock;
    }

    protected function styleToArray(string $style): array
    {
        $styleArray = [];
        foreach (explode(';', $style) as $part) {
            if (!empty($part)) {
                [$paramName, $paramValue] = explode(':', $part);
                $styleArray[trim($paramName)] = trim($paramValue);
            }
        }
        return $styleArray;
    }

    protected function getStyleValue(string $style, string $param): string
    {
        $value = '';
        if (!empty($style)) {
            $styleArray = $this->styleToArray($style);
            $value = $styleArray[$param] ?? '';
        }
        return $value;
    }

    protected function loadBlockProperties(AbstractBlock $block, string $style): void
    {
        $block->setWidth($this->getStyleValue($style, 'width'));
        $block->setHeight($this->getStyleValue($style, 'height'));
        $block->setTop($this->getStyleValue($style, 'top'));
        $block->setLeft($this->getStyleValue($style, 'left'));
    }
}
