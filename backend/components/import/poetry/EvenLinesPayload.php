<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

class EvenLinesPayload implements PoetryPayloadInterface
{
    /** @var PayloadFormatter */
    private $payloadFormatter;

    public function __construct(PayloadFormatter $payloadFormatter)
    {
        $this->payloadFormatter = $payloadFormatter;
    }

    public function createPayload(array $words): array
    {
        $contents = [];
        $fragments = [];
        foreach ($words as $i => $word) {
            $name = $word->name;
            if ($i % 2 === 1) {
                $id = $this->payloadFormatter->createUuid();
                $name = '{' . $id . '}';
                $fragments[] = $this->payloadFormatter->createFragment($id, $word->name, true);
            }
            $contents[] = '<div class="poetry-line">' . $name . '</div>';
        }
        return $this->payloadFormatter->format(implode('', $contents), $fragments);
    }
}
