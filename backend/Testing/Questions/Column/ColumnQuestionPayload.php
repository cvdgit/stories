<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column;

use DomainException;

class ColumnQuestionPayload implements \JsonSerializable
{
    /**
     * @var string
     */
    private $firstDigit;
    /**
     * @var string
     */
    private $secondDigit;
    /**
     * @var string
     */
    private $sign;
    /**
     * @var string
     */
    private $result;
    /**
     * @var array
     */
    private $steps;

    private $signs = ['+', '-', '*', '/'];

    public function __construct(
        string $firstDigit,
        string $secondDigit,
        string $sign,
        string $result,
        array $steps = []
    ) {
        $this->firstDigit = $firstDigit;
        $this->secondDigit = $secondDigit;

        if (!in_array($sign, $this->signs)) {
            throw new DomainException('Unsupported sign');
        }
        $this->sign = $sign;

        $this->result = $result;
        $this->steps = $steps;
    }

    public static function multiplyColumnSteps(string $a, string $b): array
    {
        $num1 = $a;
        $num2 = $b;
        $n1 = mb_strlen($num1);
        $n2 = mb_strlen($num2);
        $steps = [];
        for ($i = $n2 - 1; $i >= 0; $i--) {
            $partial = '';
            $carry = 0;
            $digit2 = +$num2[$i];
            for ($j = $n1 - 1; $j >= 0; $j--) {
                $digit1 = +$num1[$j];
                $mul = $digit1 * $digit2 + $carry;
                $carry = floor($mul / 10);
                $partial = ($mul % 10) . $partial;
            }
            if ($carry > 0) {
                $partial = $carry . $partial;
            }
            $partialInt = (int) $partial;
            $partial .= str_pad('', $n2 - 1 - $i, '0');
            $steps[] = [
                'step' => $n2 - $i,
                'firstDigit' => (int) $num1,
                'secondDigit' => (int) $digit2,
                'result' => (int) $partial,
                'resultInt' => $partialInt,
            ];
        }
        return $steps;
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    public function getFirstDigit(): string
    {
        return $this->firstDigit;
    }

    public function getSecondDigit(): string
    {
        return $this->secondDigit;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function asArray(): array
    {
        return [
            'firstDigit' => $this->firstDigit,
            'secondDigit' => $this->secondDigit,
            'sign' => $this->sign,
            'result' => $this->result,
            'steps' => $this->steps,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['firstDigit'],
            $payload['secondDigit'],
            $payload['sign'],
            $payload['result'],
            $payload['steps'] ?? [],
        );
    }

    public function isMultiplySign(): bool
    {
        return $this->sign === '*';
    }

    public function withSteps(): self
    {
        $steps = self::multiplyColumnSteps($this->firstDigit, $this->secondDigit);
        return new self($this->firstDigit, $this->secondDigit, $this->sign, $this->result, $steps);
    }

    public function __toString(): string
    {
        return $this->firstDigit . $this->sign . $this->secondDigit . '=' . $this->result;
    }
}
