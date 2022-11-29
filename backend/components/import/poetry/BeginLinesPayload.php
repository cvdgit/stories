<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

class BeginLinesPayload implements PoetryPayloadInterface
{
    /** @var PayloadFormatter */
    private $payloadFormatter;

    /** @var LineBreaker */
    private $lineBreaker;

    public function __construct(PayloadFormatter $payloadFormatter, LineBreaker $lineBreaker)
    {
        $this->payloadFormatter = $payloadFormatter;
        $this->lineBreaker = $lineBreaker;
    }

    public function createPayload(array $words): array
    {
        $contents = [];
        $fragments = [];
        foreach ($words as $word) {
            $name = $word->name;

            $id = $this->payloadFormatter->createUuid();
            $parts = $this->lineBreaker->wordSafeBreak($name);
            if ($parts[1] === false) {
                $name = '{' . $id . '}';
                $fragments[] = $this->payloadFormatter->createFragment($id, $parts[0], true);
            } else {
                $name = $parts[0] . ' {' . $id . '}';
                $fragments[] = $this->payloadFormatter->createFragment($id, $parts[1] === false ? $parts[0] : $parts[1], true);
            }

            $contents[] = '<div class="poetry-line">' . $name . '</div>';
        }
        return $this->payloadFormatter->format(implode('', $contents), $fragments);
    }
}
